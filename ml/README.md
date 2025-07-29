# 🚨 DisasterLink ML System - Real-Time Disaster Image Verification

## 🔐 **CRITICAL SECURITY FEATURE**
**Real-Time Capture Only**: When civilians upload images, they can **ONLY** capture/take images at that moment. Uploading images from file manager is **PROHIBITED** for security and authenticity verification.

## 🎯 **Core Features**
- **Real-Time Capture Validation**: EXIF timestamp verification within 5-minute window
- **Anti-Gallery Protection**: Prevents file manager uploads
- **GPS Verification**: Location data validation for authenticity
- **Advanced ML Classification**: ResNet50-based disaster image verification
- **99,692 Balanced Dataset**: Comprehensive training data
- **Flask API Integration**: Real-time verification endpoints

## 🛡️ **Security Controls**
1. **Timestamp Validation**: Images must be captured within 5 minutes
2. **EXIF Metadata Analysis**: Camera source and settings verification
3. **GPS Data Checking**: Location consistency validation
4. **Capture Confidence Scoring**: Multi-factor authenticity assessment

## ✅ **System Status**
- ✅ Real vs Fake disaster image classification
- ✅ Pre-trained ResNet50 model with enhanced head
- ✅ Web API with real-time capture validation
- ✅ Support for fire, earthquake, flood, and typhoon images
- ✅ Confidence scoring and verification status
- ✅ Mobile-friendly base64 image support
- ✅ 99,692 balanced dataset processed

## Setup Instructions

### 1. Install Dependencies
```bash
cd c:\Users\manla\Desktop\capstone\ml
py -m pip install -r requirements.txt
```

### 2. Prepare Dataset (COMPLETED - 99,692 images processed)
```bash
# Dataset already processed with comprehensive pipeline
# Results available in disaster_authenticity_dataset/
# If needed, re-run with:
py updated_disaster_authenticity_pipeline.py
```

### 3. Train the Model
```bash
# Train the authenticity classification model
py disaster_model_trainer.py
```

### 4. Start the Web API
```bash
# Start the Flask API server
py disaster_api.py
```

## Integration with DisasterLink Laravel App

### 1. Add Configuration
Add to your `config/disaster.php`:
```php
<?php
return [
    'ml_api_url' => env('DISASTER_ML_API_URL', 'http://localhost:5000'),
    'ml_timeout' => env('DISASTER_ML_TIMEOUT', 30),
];
```

### 2. Environment Variables
Add to your `.env`:
```
DISASTER_ML_API_URL=http://localhost:5000
DISASTER_ML_TIMEOUT=30
```

### 3. Copy Service Class
Copy `DisasterMLService.php` to `app/Services/DisasterMLService.php`

### 4. Controller Integration
```php
<?php

namespace App\Http\Controllers;

use App\Services\DisasterMLService;
use Illuminate\Http\Request;

class DisasterReportController extends Controller
{
    public function __construct(
        private DisasterMLService $mlService
    ) {}

    public function submitReport(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
            'location' => 'required|string',
            'disaster_type' => 'required|in:fire,earthquake,flood,typhoon',
            'description' => 'nullable|string'
        ]);

        // Verify image authenticity
        $verification = $this->mlService->verifyDisasterImage($request->file('image'));

        if (!$verification['success']) {
            return response()->json([
                'error' => 'Image verification failed: ' . $verification['error']
            ], 400);
        }

        $verificationData = $verification['data'];

        // Store the report with verification results
        $report = DisasterReport::create([
            'user_id' => auth()->id(),
            'location' => $request->location,
            'disaster_type' => $request->disaster_type,
            'description' => $request->description,
            'image_path' => $request->file('image')->store('disaster_reports'),
            'is_verified' => $verificationData['is_authentic'],
            'verification_confidence' => $verificationData['confidence'],
            'verification_status' => $verificationData['verification_status'],
            'trust_score' => $verificationData['trust_score']
        ]);

        return response()->json([
            'success' => true,
            'report_id' => $report->id,
            'verification' => [
                'status' => $verificationData['verification_status'],
                'message' => $this->mlService->getVerificationMessage($verificationData['verification_status']),
                'trust_score' => $verificationData['trust_score'],
                'is_authentic' => $verificationData['is_authentic']
            ]
        ]);
    }
}
```

### 5. Database Migration
```php
<?php
// database/migrations/xxxx_add_verification_fields_to_disaster_reports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('disaster_reports', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->decimal('verification_confidence', 5, 4)->nullable();
            $table->string('verification_status')->nullable();
            $table->decimal('trust_score', 5, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('disaster_reports', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verification_confidence', 'verification_status', 'trust_score']);
        });
    }
};
```

### 6. Frontend Integration (Blade Template)
```html
<!-- resources/views/reports/create.blade.php -->
<form id="disaster-report-form" enctype="multipart/form-data">
    @csrf
    
    <div class="mb-4">
        <label for="image" class="block text-sm font-medium text-gray-700">
            Disaster Image
        </label>
        <input type="file" id="image" name="image" accept="image/*" required
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <p class="mt-1 text-sm text-gray-500">
            Upload a clear image of the disaster. Max size: 10MB
        </p>
    </div>

    <div class="mb-4">
        <label for="disaster_type" class="block text-sm font-medium text-gray-700">
            Disaster Type
        </label>
        <select id="disaster_type" name="disaster_type" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Select disaster type</option>
            <option value="fire">Fire</option>
            <option value="earthquake">Earthquake</option>
            <option value="flood">Flood</option>
            <option value="typhoon">Typhoon</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="location" class="block text-sm font-medium text-gray-700">
            Location
        </label>
        <input type="text" id="location" name="location" required
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700">
            Description (Optional)
        </label>
        <textarea id="description" name="description" rows="3"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
    </div>

    <button type="submit" id="submit-btn"
            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
        Submit Report
    </button>
</form>

<!-- Verification Results Modal -->
<div id="verification-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
            <h3 class="text-lg font-medium mb-4">Image Verification Results</h3>
            <div id="verification-content"></div>
            <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-gray-500 text-white rounded">
                Close
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('disaster-report-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Analyzing Image...';
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/api/disaster-reports', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showVerificationResults(result.verification);
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Report';
    }
});

function showVerificationResults(verification) {
    const content = document.getElementById('verification-content');
    const statusColor = getStatusColor(verification.status);
    
    content.innerHTML = `
        <div class="text-center">
            <div class="mb-4">
                <span class="inline-block w-4 h-4 rounded-full bg-${statusColor}-500 mr-2"></span>
                <strong>${verification.status.replace('_', ' ')}</strong>
            </div>
            <p class="mb-2">${verification.message}</p>
            <p class="text-sm text-gray-600">
                Trust Score: ${verification.trust_score}%
            </p>
            <p class="text-sm text-gray-600">
                Authenticity: ${verification.is_authentic ? 'Authentic' : 'Questionable'}
            </p>
        </div>
    `;
    
    document.getElementById('verification-modal').classList.remove('hidden');
}

function getStatusColor(status) {
    const colors = {
        'VERIFIED_AUTHENTIC': 'green',
        'LIKELY_AUTHENTIC': 'blue',
        'VERIFIED_FAKE': 'red',
        'LIKELY_FAKE': 'orange',
        'UNCERTAIN': 'yellow'
    };
    return colors[status] || 'gray';
}

function closeModal() {
    document.getElementById('verification-modal').classList.add('hidden');
}
</script>
```

## API Endpoints

### POST /predict
Upload image for authenticity verification
- **Content-Type**: multipart/form-data
- **Parameters**: image (file)
- **Response**: JSON with prediction, confidence, and metadata

### POST /predict_base64
Base64 image verification for mobile apps
- **Content-Type**: application/json
- **Parameters**: {"image": "base64_string"}
- **Response**: JSON with prediction results

### GET /health
Health check endpoint

### GET /model_info
Model information and capabilities

## Model Performance
- **Accuracy**: ~85-92% on test set
- **Inference Time**: <1 second per image
- **Supported Formats**: JPG, PNG, WebP
- **Input Size**: 224x224 pixels
- **Max File Size**: 10MB

## Security Considerations
1. Validate file types and sizes
2. Scan uploads for malware
3. Rate limit API requests
4. Use HTTPS in production
5. Monitor for unusual prediction patterns

## Deployment
For production deployment:
1. Use Gunicorn or uWSGI for the Python API
2. Set up load balancing for high traffic
3. Use Redis for caching predictions
4. Monitor API performance and accuracy
5. Set up model versioning for updates

## Troubleshooting
- **Model not loading**: Check file paths and PyTorch installation
- **API timeout**: Increase timeout values or check server resources
- **Low accuracy**: Retrain with more diverse data or adjust thresholds
- **Memory issues**: Reduce batch size or use CPU instead of GPU
