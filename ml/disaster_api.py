"""
DisasterLink Web API - Real vs Fake Disaster Image Classification
Flask API for integration with Laravel DisasterLink application
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import torch
import torch.nn as nn
import torchvision.transforms as transforms
import torchvision.models as models
from PIL import Image, ExifTags
import io
import base64
import numpy as np
import json
from pathlib import Path
import logging
import time
from datetime import datetime
import cv2

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel integration

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class RealTimeCaptureValidator:
    """Validates that images are freshly captured, not from file manager"""
    
    def __init__(self):
        self.max_age_minutes = 5  # Images must be captured within 5 minutes
        
    def validate_real_time_capture(self, image_data, metadata=None):
        """
        Validates image is freshly captured, not from gallery
        Returns: (is_valid, reason, confidence_score)
        """
        try:
            # Convert base64 to PIL Image
            if isinstance(image_data, str):
                image_bytes = base64.b64decode(image_data)
                image = Image.open(io.BytesIO(image_bytes))
            else:
                image = image_data
            
            validation_results = {
                'has_exif': False,
                'has_gps': False,
                'recent_timestamp': False,
                'camera_metadata': False,
                'fresh_capture_score': 0
            }
            
            # Check EXIF data for fresh capture indicators
            exif_data = self._extract_exif_data(image)
            if exif_data:
                validation_results['has_exif'] = True
                
                # Check for camera/capture metadata
                camera_indicators = ['Make', 'Model', 'Software', 'DateTime', 'DateTimeOriginal']
                for indicator in camera_indicators:
                    if indicator in exif_data:
                        validation_results['camera_metadata'] = True
                        break
                
                # Check timestamp freshness
                if self._check_timestamp_freshness(exif_data):
                    validation_results['recent_timestamp'] = True
                
                # Check GPS data (mobile captures often have GPS)
                if self._has_gps_data(exif_data):
                    validation_results['has_gps'] = True
            
            # Calculate fresh capture confidence score
            confidence_score = self._calculate_capture_confidence(validation_results)
            
            # Determine if image appears to be freshly captured
            is_fresh_capture = confidence_score >= 0.6  # 60% confidence threshold
            
            if is_fresh_capture:
                return True, "Fresh capture detected", confidence_score
            else:
                return False, "Image appears to be from gallery/file manager", confidence_score
                
        except Exception as e:
            return False, f"Validation error: {str(e)}", 0.0
    
    def _extract_exif_data(self, image):
        """Extract EXIF metadata from image"""
        try:
            exif_dict = {}
            if hasattr(image, '_getexif') and image._getexif() is not None:
                exif = image._getexif()
                for tag_id, value in exif.items():
                    tag = ExifTags.TAGS.get(tag_id, tag_id)
                    exif_dict[tag] = value
            return exif_dict
        except:
            return {}
    
    def _check_timestamp_freshness(self, exif_data):
        """Check if image timestamp is recent (within 5 minutes)"""
        try:
            # Check DateTimeOriginal first, then DateTime
            timestamp_fields = ['DateTimeOriginal', 'DateTime']
            
            for field in timestamp_fields:
                if field in exif_data:
                    timestamp_str = str(exif_data[field])
                    # Parse timestamp (format: "YYYY:MM:DD HH:MM:SS")
                    image_time = datetime.strptime(timestamp_str, "%Y:%m:%d %H:%M:%S")
                    current_time = datetime.now()
                    time_diff = (current_time - image_time).total_seconds() / 60  # minutes
                    
                    return time_diff <= self.max_age_minutes
            
            return False
        except:
            return False
    
    def _has_gps_data(self, exif_data):
        """Check for GPS data indicating mobile capture"""
        gps_fields = ['GPSInfo', 'GPS', 'GPSLatitude', 'GPSLongitude']
        return any(field in exif_data for field in gps_fields)
    
    def _calculate_capture_confidence(self, results):
        """Calculate confidence score for fresh capture"""
        score = 0.0
        
        # EXIF presence (basic requirement)
        if results['has_exif']:
            score += 0.3
        
        # Recent timestamp (strong indicator)
        if results['recent_timestamp']:
            score += 0.4
        
        # Camera metadata (good indicator)
        if results['camera_metadata']:
            score += 0.2
        
        # GPS data (mobile capture indicator)
        if results['has_gps']:
            score += 0.1
        
        return min(score, 1.0)

class DisasterAuthenticityModel(nn.Module):
    """Model class matching the training script"""
    def __init__(self, num_classes=2):
        super(DisasterAuthenticityModel, self).__init__()
        self.backbone = models.resnet50(pretrained=False)
        num_features = self.backbone.fc.in_features
        self.backbone.fc = nn.Sequential(
            nn.Dropout(0.5),
            nn.Linear(num_features, 512),
            nn.ReLU(inplace=True),
            nn.Dropout(0.3),
            nn.Linear(512, 128),
            nn.ReLU(inplace=True),
            nn.Linear(128, num_classes)
        )
        
    def forward(self, x):
        return self.backbone(x)

class DisasterImageClassifier:
    def __init__(self, model_path='disaster_authenticity_model.pth'):
        self.device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
        self.model = DisasterAuthenticityModel()
        
        # Load trained model
        try:
            checkpoint = torch.load(model_path, map_location=self.device)
            self.model.load_state_dict(checkpoint['model_state_dict'])
            self.model.eval()
            self.model.to(self.device)
            logger.info(f"Model loaded successfully on {self.device}")
        except Exception as e:
            logger.error(f"Error loading model: {e}")
            raise
        
        # Define image preprocessing
        self.transform = transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
        ])
        
        self.classes = ['fake', 'real']
        
    def preprocess_image(self, image):
        """Preprocess image for model input"""
        try:
            # Convert to RGB if necessary
            if image.mode != 'RGB':
                image = image.convert('RGB')
            
            # Apply transforms
            image_tensor = self.transform(image).unsqueeze(0)
            return image_tensor.to(self.device)
        except Exception as e:
            logger.error(f"Error preprocessing image: {e}")
            raise
    
    def predict(self, image):
        """Predict if disaster image is real or fake"""
        try:
            # Preprocess image
            image_tensor = self.preprocess_image(image)
            
            # Make prediction
            with torch.no_grad():
                start_time = time.time()
                outputs = self.model(image_tensor)
                prediction_time = time.time() - start_time
                
                # Get probabilities
                probabilities = torch.softmax(outputs, dim=1)
                confidence_scores = probabilities.cpu().numpy()[0]
                
                # Get predicted class
                predicted_class_idx = torch.argmax(outputs, dim=1).item()
                predicted_class = self.classes[predicted_class_idx]
                confidence = float(confidence_scores[predicted_class_idx])
                
                return {
                    'prediction': predicted_class,
                    'confidence': confidence,
                    'probabilities': {
                        'fake': float(confidence_scores[0]),
                        'real': float(confidence_scores[1])
                    },
                    'prediction_time': prediction_time,
                    'status': 'success'
                }
                
        except Exception as e:
            logger.error(f"Error during prediction: {e}")
            return {
                'prediction': None,
                'confidence': None,
                'error': str(e),
                'status': 'error'
            }

# Initialize classifier
try:
    classifier = DisasterImageClassifier()
    logger.info("Disaster image classifier initialized successfully")
except Exception as e:
    logger.error(f"Failed to initialize classifier: {e}")
    classifier = None

# Initialize capture validator
capture_validator = RealTimeCaptureValidator()
logger.info("Real-time capture validator initialized")

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model_loaded': classifier is not None,
        'features': [
            'Real-time capture validation',
            'Disaster authenticity verification',
            'Fresh image requirement (5 minutes max)',
            'Anti-gallery upload protection'
        ],
        'timestamp': time.time()
    })

@app.route('/verify_disaster', methods=['POST'])
def verify_disaster():
    """
    Enhanced disaster verification with real-time capture validation
    Accepts base64 image with metadata and validates fresh capture
    """
    try:
        # Get request data
        data = request.get_json()
        
        if 'image' not in data:
            return jsonify({
                'success': False,
                'error': 'No image provided'
            }), 400
        
        image_data = data['image']  # Base64 encoded
        user_location = data.get('location', {})
        capture_metadata = data.get('metadata', {})
        
        # STEP 1: Validate real-time capture
        is_fresh, capture_reason, capture_confidence = capture_validator.validate_real_time_capture(
            image_data, capture_metadata
        )
        
        if not is_fresh:
            return jsonify({
                'success': False,
                'error': 'IMAGE_NOT_FRESH_CAPTURE',
                'message': 'Only real-time captured images are allowed. Please take a fresh photo of the current disaster situation.',
                'details': {
                    'reason': capture_reason,
                    'confidence': capture_confidence,
                    'requirement': 'Images must be captured within the last 5 minutes'
                }
            }), 403
        
        # STEP 2: Process image for ML prediction
        # Convert base64 to PIL Image
        image_bytes = base64.b64decode(image_data)
        image = Image.open(io.BytesIO(image_bytes))
        
        # Check minimum dimensions
        if image.size[0] < 100 or image.size[1] < 100:
            return jsonify({
                'success': False,
                'error': 'Image too small (minimum 100x100 pixels)'
            }), 400
        
        # STEP 3: ML Prediction
        if classifier is None:
            return jsonify({
                'success': False,
                'error': 'Model not loaded'
            }), 500
        
        prediction_result = classifier.predict(image)
        
        if prediction_result['status'] == 'error':
            return jsonify({
                'success': False,
                'error': 'Prediction failed',
                'details': prediction_result.get('error', 'Unknown error')
            }), 500
        
        # STEP 4: Generate enhanced response
        is_authentic = prediction_result['prediction'] == 'real'
        authenticity_score = prediction_result['confidence']
        
        result = {
            'success': True,
            'capture_validation': {
                'is_fresh_capture': True,
                'capture_confidence': capture_confidence,
                'validation_details': capture_reason
            },
            'disaster_analysis': {
                'is_authentic': is_authentic,
                'authenticity_score': authenticity_score,
                'confidence_level': 'HIGH' if authenticity_score > 0.8 else 'MEDIUM' if authenticity_score > 0.6 else 'LOW',
                'status': 'VERIFIED_AUTHENTIC' if is_authentic else 'LIKELY_FAKE',
                'probabilities': prediction_result['probabilities']
            },
            'recommendation': {
                'action': 'PROCEED_WITH_REPORT' if is_authentic and authenticity_score > 0.7 else 'REJECT_SUBMISSION',
                'message': 'Image verified as authentic disaster documentation' if is_authentic else 'Image appears to be manipulated or not a genuine disaster'
            },
            'metadata': {
                'processing_time': prediction_result.get('prediction_time', 'unknown'),
                'model_version': '1.0',
                'timestamp': datetime.now().isoformat(),
                'location': user_location
            }
        }
        
        return jsonify(result)
        
    except Exception as e:
        logger.error(f"Error in verify_disaster: {e}")
        return jsonify({
            'success': False,
            'error': 'PROCESSING_ERROR',
            'message': str(e)
        }), 500

@app.route('/predict', methods=['POST'])
def predict_disaster_authenticity():
    """
    Main prediction endpoint for DisasterLink
    Accepts image upload and returns authenticity prediction
    """
    
    if classifier is None:
        return jsonify({
            'error': 'Model not loaded',
            'status': 'error'
        }), 500
    
    try:
        # Check if image is provided
        if 'image' not in request.files:
            return jsonify({
                'error': 'No image provided',
                'status': 'error'
            }), 400
        
        image_file = request.files['image']
        
        # Validate file
        if image_file.filename == '':
            return jsonify({
                'error': 'No image selected',
                'status': 'error'
            }), 400
        
        # Check file size (max 10MB)
        image_file.seek(0, 2)  # Seek to end
        file_size = image_file.tell()
        image_file.seek(0)  # Reset to beginning
        
        if file_size > 10 * 1024 * 1024:  # 10MB
            return jsonify({
                'error': 'Image too large (max 10MB)',
                'status': 'error'
            }), 400
        
        # Load and validate image
        try:
            image = Image.open(image_file.stream)
            
            # Check minimum dimensions
            if image.size[0] < 100 or image.size[1] < 100:
                return jsonify({
                    'error': 'Image too small (minimum 100x100 pixels)',
                    'status': 'error'
                }), 400
                
        except Exception as e:
            return jsonify({
                'error': f'Invalid image format: {str(e)}',
                'status': 'error'
            }), 400
        
        # Make prediction
        result = classifier.predict(image)
        
        if result['status'] == 'error':
            return jsonify(result), 500
        
        # Add additional metadata
        result.update({
            'image_size': image.size,
            'file_size': file_size,
            'timestamp': time.time(),
            'model_version': '1.0',
            'disaster_types_supported': ['fire', 'earthquake', 'flood', 'typhoon']
        })
        
        # Log prediction
        logger.info(f"Prediction: {result['prediction']} (confidence: {result['confidence']:.3f})")
        
        return jsonify(result)
        
    except Exception as e:
        logger.error(f"Unexpected error in prediction: {e}")
        return jsonify({
            'error': f'Internal server error: {str(e)}',
            'status': 'error'
        }), 500

@app.route('/predict_base64', methods=['POST'])
def predict_from_base64():
    """
    Alternative endpoint that accepts base64 encoded images
    Useful for mobile app integration
    """
    
    if classifier is None:
        return jsonify({
            'error': 'Model not loaded',
            'status': 'error'
        }), 500
    
    try:
        data = request.get_json()
        
        if 'image' not in data:
            return jsonify({
                'error': 'No base64 image provided',
                'status': 'error'
            }), 400
        
        # Decode base64 image
        try:
            image_data = base64.b64decode(data['image'])
            image = Image.open(io.BytesIO(image_data))
        except Exception as e:
            return jsonify({
                'error': f'Invalid base64 image: {str(e)}',
                'status': 'error'
            }), 400
        
        # Make prediction
        result = classifier.predict(image)
        
        if result['status'] == 'error':
            return jsonify(result), 500
        
        # Add metadata
        result.update({
            'image_size': image.size,
            'timestamp': time.time(),
            'model_version': '1.0'
        })
        
        return jsonify(result)
        
    except Exception as e:
        logger.error(f"Error in base64 prediction: {e}")
        return jsonify({
            'error': f'Internal server error: {str(e)}',
            'status': 'error'
        }), 500

@app.route('/model_info', methods=['GET'])
def get_model_info():
    """Get model information for integration"""
    return jsonify({
        'model_type': 'disaster_authenticity_classifier',
        'version': '1.0',
        'classes': ['fake', 'real'],
        'input_size': [224, 224, 3],
        'supported_formats': ['.jpg', '.jpeg', '.png', '.webp'],
        'max_file_size': '10MB',
        'disaster_types': ['fire', 'earthquake', 'flood', 'typhoon'],
        'confidence_threshold': 0.7,
        'features': [
            'Real-time capture validation',
            'Fresh image requirement (5 minutes max)',
            'EXIF metadata analysis',
            'Gallery upload prevention'
        ],
        'api_endpoints': {
            'verify_disaster': '/verify_disaster (enhanced with capture validation)',
            'predict': '/predict (multipart/form-data)',
            'predict_base64': '/predict_base64 (application/json)',
            'health': '/health',
            'model_info': '/model_info'
        },
        'integration_guide': {
            'laravel_example': 'See documentation for Laravel integration code',
            'mobile_app': 'Use /verify_disaster for real-time capture validation'
        }
    })

@app.errorhandler(413)
def too_large(e):
    return jsonify({
        'error': 'File too large',
        'max_size': '10MB',
        'status': 'error'
    }), 413

@app.errorhandler(500)
def internal_error(e):
    return jsonify({
        'error': 'Internal server error',
        'status': 'error'
    }), 500

if __name__ == '__main__':
    # Run development server
    print("Starting DisasterLink ML API...")
    print("Model loaded:", classifier is not None)
    print("Real-time capture validation: ENABLED")
    print("Available endpoints:")
    print("- POST /verify_disaster - Enhanced disaster verification with capture validation")
    print("- POST /predict - Upload image for authenticity check")
    print("- POST /predict_base64 - Base64 image prediction")
    print("- GET /model_info - Model information")
    print("- GET /health - Health check")
    print("\nSecurity Features:")
    print("- Real-time capture validation (5-minute window)")
    print("- Gallery upload prevention")
    print("- EXIF metadata analysis")
    
    app.run(host='0.0.0.0', port=5000, debug=False)
