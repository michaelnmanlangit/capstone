#!/usr/bin/env python3
"""
DisasterLink ML Training Pipeline
Enhanced training script with real-time monitoring
"""

import os
import sys
from pathlib import Path

# Add current directory to path
sys.path.append(str(Path(__file__).parent))

from disaster_model_trainer import DisasterModelTrainer
import json

def main():
    print("🚀 Starting DisasterLink ML Training Pipeline")
    print("=" * 60)
    
    # Load configuration
    with open('model_config.json', 'r') as f:
        config = json.load(f)
    
    print(f"📊 Dataset: {config['dataset_info']['total_samples']} samples")
    print(f"🎯 Target Accuracy: {config['model_performance']['target_accuracy']}")
    print(f"🔐 Real-Time Validation: {'Enabled' if config['real_time_validation']['enabled'] else 'Disabled'}")
    print("=" * 60)
    
    # Initialize trainer
    trainer = DisasterModelTrainer(
        model_type='resnet50',
        device=None  # Auto-detect
    )
    
    # Check dataset
    dataset_path = Path('disaster_authenticity_dataset')
    if not dataset_path.exists():
        print("❌ Dataset not found! Please run the data preparation pipeline first.")
        return
    
    train_csv = dataset_path / 'train_dataset.csv'
    val_csv = dataset_path / 'val_dataset.csv'
    test_csv = dataset_path / 'test_dataset.csv'
    
    if not all([train_csv.exists(), val_csv.exists(), test_csv.exists()]):
        print("❌ Dataset files incomplete! Please check dataset preparation.")
        return
    
    print("✅ Dataset files found and ready")
    
    # Start training
    try:
        # Load datasets first
        print("📂 Loading datasets...")
        trainer.load_datasets('disaster_authenticity_dataset')
        
        # Train the model
        print("🎯 Starting training...")
        history = trainer.train_model(
            num_epochs=config['training_config']['num_epochs'],
            learning_rate=config['training_config']['learning_rate']
        )
        
        # Evaluate the model
        print("📊 Evaluating model...")
        test_acc, report = trainer.evaluate_model()
        
        # Save for web deployment
        print("💾 Saving model for deployment...")
        trainer.save_web_model('disaster_authenticity_model.pth')
        
        print("🎉 Training completed successfully!")
        print(f"🏆 Best validation accuracy: {history['best_val_acc']:.2f}%")
        print(f"🎯 Test accuracy: {test_acc:.2f}%")
        
        # Update API status
        print("🔄 Updating API model status...")
        
        # Create model info file for API
        model_info = {
            "model_loaded": True,
            "model_path": "disaster_authenticity_model.pth",
            "accuracy": f"Test accuracy: {test_acc:.2f}%",
            "best_val_accuracy": f"{history['best_val_acc']:.2f}%",
            "training_date": "July 22, 2025"
        }
        
        with open('model_status.json', 'w') as f:
            json.dump(model_info, f, indent=2)
        
        print("✅ Model training pipeline completed!")
        print("🚀 You can now restart the API to load the trained model")
        
    except Exception as e:
        print(f"❌ Training failed: {e}")
        return

if __name__ == "__main__":
    main()
