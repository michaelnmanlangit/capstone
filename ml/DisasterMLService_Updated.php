<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

/**
 * DisasterLink ML Service
 * Enhanced with real-time capture validation for civilian disaster reporting
 */
class DisasterMLService
{
    private string $apiUrl;
    private int $timeout;
    private array $supportedFormats;
    private int $maxFileSize;

    public function __construct()
    {
        $this->apiUrl = config('disaster.ml_api_url', 'http://localhost:5000');
        $this->timeout = config('disaster.ml_timeout', 30);
        $this->supportedFormats = ['jpg', 'jpeg', 'png', 'webp'];
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
    }

    /**
     * Verify disaster image authenticity with real-time capture validation
     * 
     * @param string $base64Image - Base64 encoded image
     * @param array $captureMetadata - Metadata from mobile capture
     * @param array $userLocation - User's GPS location
     * @return array
     */
    public function verifyDisasterImage($base64Image, $captureMetadata = [], $userLocation = []): array
    {
        try {
            // Prepare request data
            $requestData = [
                'image' => $base64Image,
                'metadata' => array_merge($captureMetadata, [
                    'submission_time' => Carbon::now()->toISOString(),
                    'client_type' => 'mobile_app'
                ]),
                'location' => $userLocation
            ];
            
            // Call enhanced ML API
            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/verify_disaster', $requestData);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Log successful verification
                Log::info('Disaster image verification completed', [
                    'is_authentic' => $result['disaster_analysis']['is_authentic'] ?? false,
                    'capture_validated' => $result['capture_validation']['is_fresh_capture'] ?? false,
                    'authenticity_score' => $result['disaster_analysis']['authenticity_score'] ?? 0
                ]);
                
                return $this->formatVerificationResult($result);
            } else {
                // Handle API errors
                $errorData = $response->json();
                
                if ($response->status() === 403 && isset($errorData['error']) && $errorData['error'] === 'IMAGE_NOT_FRESH_CAPTURE') {
                    return [
                        'success' => false,
                        'error_type' => 'NOT_FRESH_CAPTURE',
                        'message' => 'Please take a fresh photo of the current disaster situation. Uploaded images from gallery are not allowed.',
                        'requirements' => [
                            'capture_method' => 'real_time_camera_only',
                            'max_age' => '5_minutes',
                            'gallery_uploads' => 'prohibited'
                        ]
                    ];
                }
                
                throw new Exception('ML API error: ' . $response->body());
            }
            
        } catch (Exception $e) {
            Log::error('Disaster ML verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error_type' => 'VERIFICATION_FAILED',
                'message' => 'Unable to verify image authenticity. Please try again.',
                'technical_error' => $e->getMessage()
            ];
        }
    }

    /**
     * Legacy method for uploaded file verification (still available for testing)
     */
    public function verifyUploadedImage(UploadedFile $image): array
    {
        try {
            // Validate image
            $validation = $this->validateImage($image);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'error' => $validation['message'],
                    'code' => 'VALIDATION_ERROR'
                ];
            }

            // Make API request to legacy endpoint
            $response = Http::timeout($this->timeout)
                ->attach('image', fopen($image->getRealPath(), 'r'), $image->getClientOriginalName())
                ->post($this->apiUrl . '/predict');

            if (!$response->successful()) {
                throw new Exception('ML API request failed: ' . $response->body());
            }

            $result = $response->json();

            if ($result['status'] === 'error') {
                throw new Exception($result['error']);
            }

            // Format response
            return $this->formatLegacyResult($result);

        } catch (Exception $e) {
            Log::error('ML API verification failed', [
                'error' => $e->getMessage(),
                'image_name' => $image->getClientOriginalName()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 'API_ERROR'
            ];
        }
    }
    
    /**
     * Format verification result for DisasterLink application
     */
    private function formatVerificationResult($apiResult): array
    {
        $isAuthentic = $apiResult['disaster_analysis']['is_authentic'] ?? false;
        $authenticityScore = $apiResult['disaster_analysis']['authenticity_score'] ?? 0;
        $captureConfidence = $apiResult['capture_validation']['capture_confidence'] ?? 0;
        
        // Determine overall trust level
        $trustLevel = $this->calculateTrustLevel($authenticityScore, $captureConfidence);
        
        return [
            'success' => true,
            'verification' => [
                'is_authentic' => $isAuthentic,
                'authenticity_score' => round($authenticityScore * 100, 1), // Convert to percentage
                'trust_level' => $trustLevel,
                'status' => $this->getVerificationStatus($isAuthentic, $authenticityScore),
            ],
            'capture_validation' => [
                'is_fresh_capture' => $apiResult['capture_validation']['is_fresh_capture'] ?? false,
                'capture_confidence' => round($captureConfidence * 100, 1),
                'validation_message' => $apiResult['capture_validation']['validation_details'] ?? ''
            ],
            'recommendation' => [
                'action' => $apiResult['recommendation']['action'] ?? 'MANUAL_REVIEW',
                'message' => $apiResult['recommendation']['message'] ?? '',
                'priority' => $this->getReportPriority($trustLevel, $isAuthentic)
            ],
            'metadata' => [
                'processed_at' => Carbon::now(),
                'model_version' => $apiResult['metadata']['model_version'] ?? '1.0',
                'processing_time' => $apiResult['metadata']['processing_time'] ?? 'unknown'
            ]
        ];
    }

    /**
     * Format legacy result for backward compatibility
     */
    private function formatLegacyResult($result): array
    {
        $isAuthentic = $result['prediction'] === 'real';
        $confidence = $result['confidence'] ?? 0;

        return [
            'success' => true,
            'verification' => [
                'is_authentic' => $isAuthentic,
                'authenticity_score' => round($confidence * 100, 1),
                'trust_level' => $confidence > 0.8 ? 'HIGH' : ($confidence > 0.6 ? 'MEDIUM' : 'LOW'),
                'status' => $isAuthentic ? 'VERIFIED_AUTHENTIC' : 'LIKELY_FAKE'
            ],
            'probabilities' => [
                'fake' => round(($result['probabilities']['fake'] ?? 0) * 100, 1),
                'real' => round(($result['probabilities']['real'] ?? 0) * 100, 1)
            ],
            'metadata' => [
                'processed_at' => Carbon::now(),
                'prediction_time' => $result['prediction_time'] ?? 'unknown',
                'model_version' => $result['model_version'] ?? '1.0'
            ]
        ];
    }
    
    /**
     * Calculate overall trust level combining authenticity and capture validation
     */
    private function calculateTrustLevel($authenticityScore, $captureConfidence): string
    {
        $combinedScore = ($authenticityScore * 0.7) + ($captureConfidence * 0.3);
        
        if ($combinedScore >= 0.85) return 'VERY_HIGH';
        if ($combinedScore >= 0.70) return 'HIGH';
        if ($combinedScore >= 0.55) return 'MEDIUM';
        if ($combinedScore >= 0.40) return 'LOW';
        return 'VERY_LOW';
    }
    
    /**
     * Get verification status for UI display
     */
    private function getVerificationStatus($isAuthentic, $score): string
    {
        if (!$isAuthentic) return 'REJECTED_FAKE';
        
        if ($score >= 0.9) return 'VERIFIED_AUTHENTIC';
        if ($score >= 0.7) return 'LIKELY_AUTHENTIC';
        if ($score >= 0.5) return 'UNCERTAIN';
        return 'NEEDS_REVIEW';
    }
    
    /**
     * Determine report priority for emergency response
     */
    private function getReportPriority($trustLevel, $isAuthentic): string
    {
        if (!$isAuthentic) return 'REJECT';
        
        switch ($trustLevel) {
            case 'VERY_HIGH':
            case 'HIGH':
                return 'URGENT';
            case 'MEDIUM':
                return 'NORMAL';
            case 'LOW':
            case 'VERY_LOW':
                return 'LOW_PRIORITY';
            default:
                return 'MANUAL_REVIEW';
        }
    }

    /**
     * Validate that image meets real-time capture requirements
     */
    public function validateCaptureRequirements($imageMetadata): array
    {
        $requirements = [
            'fresh_capture' => false,
            'has_location' => false,
            'has_timestamp' => false,
            'from_camera' => false
        ];
        
        // Check if image has fresh timestamp
        if (isset($imageMetadata['capture_time'])) {
            $captureTime = Carbon::parse($imageMetadata['capture_time']);
            $timeDiff = Carbon::now()->diffInMinutes($captureTime);
            $requirements['fresh_capture'] = $timeDiff <= 5;
        }
        
        // Check for location data
        $requirements['has_location'] = isset($imageMetadata['latitude']) && isset($imageMetadata['longitude']);
        
        // Check for timestamp
        $requirements['has_timestamp'] = isset($imageMetadata['capture_time']);
        
        // Check camera source
        $requirements['from_camera'] = isset($imageMetadata['source']) && $imageMetadata['source'] === 'camera';
        
        return $requirements;
    }

    /**
     * Validate uploaded image file
     */
    private function validateImage(UploadedFile $image): array
    {
        // Check file size
        if ($image->getSize() > $this->maxFileSize) {
            return [
                'valid' => false,
                'message' => 'Image file too large. Maximum size: 10MB'
            ];
        }

        // Check file extension
        $extension = strtolower($image->getClientOriginalExtension());
        if (!in_array($extension, $this->supportedFormats)) {
            return [
                'valid' => false,
                'message' => 'Unsupported image format. Supported: ' . implode(', ', $this->supportedFormats)
            ];
        }

        // Verify it's actually an image
        if (!getimagesize($image->getRealPath())) {
            return [
                'valid' => false,
                'message' => 'Invalid image file'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Check if ML API is available
     */
    public function isApiAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->apiUrl . '/health');
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get ML API information
     */
    public function getApiInfo(): array
    {
        try {
            $response = Http::timeout(10)->get($this->apiUrl . '/model_info');
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            return [
                'success' => false,
                'error' => 'API not available'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Convert uploaded file to base64 for API
     */
    public function imageToBase64(UploadedFile $image): string
    {
        return base64_encode(file_get_contents($image->getRealPath()));
    }

    /**
     * Get supported disaster types
     */
    public function getSupportedDisasterTypes(): array
    {
        return ['fire', 'earthquake', 'flood', 'typhoon'];
    }
}
?>
