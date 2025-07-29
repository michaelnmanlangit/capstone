"""
Mock DisasterLink ML API for Testing Week 1 Integration
This provides the same endpoints as the full ML API but with mock responses
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import base64
import json
import time
from datetime import datetime
import random

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel integration

# Mock responses for testing
MOCK_RESPONSES = {
    'authentic_disaster': {
        'prediction': 'real',
        'confidence': 0.87,
        'probabilities': {'fake': 0.13, 'real': 0.87}
    },
    'fake_disaster': {
        'prediction': 'fake', 
        'confidence': 0.92,
        'probabilities': {'fake': 0.92, 'real': 0.08}
    }
}

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model_loaded': True,
        'mode': 'MOCK_TESTING',
        'features': [
            'Mock disaster authenticity verification',
            'Laravel integration testing',
            'Base64 image processing',
            'Real-time API simulation'
        ],
        'timestamp': time.time()
    })

@app.route('/verify_disaster', methods=['POST'])
def verify_disaster():
    """
    Mock disaster verification endpoint
    Returns realistic responses for testing
    """
    try:
        data = request.get_json()
        
        if 'image' not in data:
            return jsonify({
                'success': False,
                'error': 'No image provided'
            }), 400

        # Simulate processing time
        time.sleep(0.5)
        
        # Randomly choose authentic or fake for testing
        is_authentic = random.choice([True, False])
        mock_data = MOCK_RESPONSES['authentic_disaster'] if is_authentic else MOCK_RESPONSES['fake_disaster']
        
        result = {
            'success': True,
            'capture_validation': {
                'is_fresh_capture': True,
                'capture_confidence': 0.85,
                'validation_details': 'Mock: Fresh capture detected'
            },
            'disaster_analysis': {
                'is_authentic': is_authentic,
                'authenticity_score': mock_data['confidence'],
                'confidence_level': 'HIGH' if mock_data['confidence'] > 0.8 else 'MEDIUM',
                'status': 'VERIFIED_AUTHENTIC' if is_authentic else 'LIKELY_FAKE',
                'probabilities': mock_data['probabilities']
            },
            'recommendation': {
                'action': 'PROCEED_WITH_REPORT' if is_authentic else 'REJECT_SUBMISSION',
                'message': 'Mock: Image verified as authentic disaster documentation' if is_authentic else 'Mock: Image appears to be manipulated'
            },
            'metadata': {
                'processing_time': 0.5,
                'model_version': 'MOCK_1.0',
                'timestamp': datetime.now().isoformat(),
                'mode': 'TESTING'
            }
        }
        
        return jsonify(result)
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': 'MOCK_PROCESSING_ERROR',
            'message': str(e)
        }), 500

@app.route('/predict', methods=['POST'])
def predict_disaster_authenticity():
    """Mock prediction endpoint for file uploads"""
    
    try:
        if 'image' not in request.files:
            return jsonify({
                'error': 'No image provided',
                'status': 'error'
            }), 400
        
        image_file = request.files['image']
        
        if image_file.filename == '':
            return jsonify({
                'error': 'No image selected', 
                'status': 'error'
            }), 400

        # Simulate processing
        time.sleep(0.3)
        
        # Random prediction for testing
        is_real = random.choice([True, False])
        mock_data = MOCK_RESPONSES['authentic_disaster'] if is_real else MOCK_RESPONSES['fake_disaster']
        
        result = {
            'prediction': mock_data['prediction'],
            'confidence': mock_data['confidence'],
            'probabilities': mock_data['probabilities'],
            'prediction_time': 0.3,
            'status': 'success',
            'image_size': [224, 224],
            'file_size': 1024,
            'timestamp': time.time(),
            'model_version': 'MOCK_1.0',
            'mode': 'TESTING'
        }
        
        return jsonify(result)
        
    except Exception as e:
        return jsonify({
            'error': f'Mock error: {str(e)}',
            'status': 'error'
        }), 500

@app.route('/predict_base64', methods=['POST'])
def predict_from_base64():
    """Mock base64 prediction endpoint"""
    
    try:
        data = request.get_json()
        
        if 'image' not in data:
            return jsonify({
                'error': 'No base64 image provided',
                'status': 'error'
            }), 400
        
        # Simulate processing
        time.sleep(0.2)
        
        # Random prediction
        is_real = random.choice([True, False])
        mock_data = MOCK_RESPONSES['authentic_disaster'] if is_real else MOCK_RESPONSES['fake_disaster']
        
        result = {
            'prediction': mock_data['prediction'],
            'confidence': mock_data['confidence'], 
            'probabilities': mock_data['probabilities'],
            'prediction_time': 0.2,
            'status': 'success',
            'image_size': [224, 224],
            'timestamp': time.time(),
            'model_version': 'MOCK_1.0',
            'mode': 'TESTING'
        }
        
        return jsonify(result)
        
    except Exception as e:
        return jsonify({
            'error': f'Mock error: {str(e)}',
            'status': 'error'
        }), 500

@app.route('/model_info', methods=['GET'])
def get_model_info():
    """Mock model information"""
    return jsonify({
        'model_type': 'mock_disaster_authenticity_classifier',
        'version': 'MOCK_1.0',
        'mode': 'TESTING',
        'classes': ['fake', 'real'],
        'input_size': [224, 224, 3],
        'supported_formats': ['.jpg', '.jpeg', '.png', '.webp'],
        'max_file_size': '10MB',
        'disaster_types': ['fire', 'earthquake', 'flood', 'typhoon'],
        'confidence_threshold': 0.7,
        'features': [
            'Mock disaster verification',
            'Laravel integration testing',
            'Random predictions for testing',
            'All endpoints functional'
        ],
        'api_endpoints': {
            'verify_disaster': '/verify_disaster',
            'predict': '/predict',
            'predict_base64': '/predict_base64',
            'health': '/health',
            'model_info': '/model_info'
        },
        'testing_note': 'This is a mock API for Week 1 testing. Actual ML model integration will be completed in Week 2.'
    })

if __name__ == '__main__':
    print("🧪 Starting DisasterLink MOCK ML API for Week 1 Testing...")
    print("✅ Mock Model loaded: TRUE")
    print("🔧 Mode: TESTING")
    print("📡 Available endpoints:")
    print("- POST /verify_disaster - Mock disaster verification")
    print("- POST /predict - Mock image upload prediction")
    print("- POST /predict_base64 - Mock base64 prediction")
    print("- GET /model_info - Mock model information")
    print("- GET /health - Health check")
    print("\n🎯 Week 1 Testing Features:")
    print("- Mock ML predictions (random authentic/fake)")
    print("- Laravel integration testing")
    print("- All API endpoints functional")
    print("- Realistic response simulation")
    print("\n🚀 Ready for Laravel integration testing!")
    
    app.run(host='0.0.0.0', port=5001, debug=True)
