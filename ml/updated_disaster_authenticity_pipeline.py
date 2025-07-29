#!/usr/bin/env python3
"""
Updated Disaster Authenticity Data Pipeline for DisasterLink ML System
Processes ALL discovered disaster datasets (50,286+ images) for real vs fake classification.
Optimized for comprehensive dataset coverage and web application integration.
"""

import os
import pandas as pd
import numpy as np
from pathlib import Path
import shutil
import logging
from PIL import Image
import cv2
import albumentations as A
from albumentations.pytorch import ToTensorV2
from sklearn.model_selection import train_test_split
from sklearn.utils import shuffle
import json
from collections import defaultdict
import random

class ComprehensiveDisasterAuthenticityPipeline:
    def __init__(self, base_dir=None):
        """Initialize the comprehensive disaster authenticity pipeline."""
        # Use relative paths for cross-platform compatibility
        if base_dir is None:
            # Get the directory where this script is located
            script_dir = Path(__file__).parent.absolute()
            base_dir = script_dir / "crisis_vision_benchmarks"
        
        self.base_dir = Path(base_dir)
        self.data_dir = self.base_dir / "data"
        self.output_dir = Path(__file__).parent.absolute() / "disaster_authenticity_dataset"
        
        # Create output directories
        self.output_dir.mkdir(exist_ok=True)
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger(__name__)
        
        # Image processing parameters
        self.valid_extensions = {'.jpg', '.jpeg', '.png', '.bmp', '.gif', '.tiff', '.webp'}
        self.min_size = (100, 100)
        self.max_size = (4000, 4000)
        self.target_size = (224, 224)
        
        # Initialize data containers
        self.comprehensive_data = []
        self.dataset_stats = defaultdict(lambda: defaultdict(int))
        
        # Setup augmentation pipelines
        self.setup_augmentations()
        
        print("🚀 Comprehensive Disaster Authenticity Pipeline Initialized")
        print(f"📁 Base directory: {self.base_dir}")
        print(f"📁 Output directory: {self.output_dir}")

    def setup_augmentations(self):
        """Setup augmentation pipelines for creating fake variants."""
        # Aggressive augmentation for creating "fake" variants
        self.fake_augmentation = A.Compose([
            # Compression artifacts (common in fake/manipulated images)
            A.ImageCompression(quality_range=(10, 50), p=0.6),
            
            # Color manipulations (unrealistic colors)
            A.HueSaturationValue(hue_shift_limit=30, sat_shift_limit=40, val_shift_limit=30, p=0.8),
            A.RandomBrightnessContrast(brightness_limit=0.4, contrast_limit=0.4, p=0.8),
            A.ColorJitter(brightness=0.3, contrast=0.3, saturation=0.3, hue=0.2, p=0.6),
            
            # Geometric distortions (manipulation artifacts)
            A.Perspective(scale=(0.05, 0.2), p=0.4),
            A.OpticalDistortion(distort_limit=0.3, p=0.4),
            A.ElasticTransform(alpha=120, sigma=120 * 0.05, p=0.3),
            
            # Noise and blur (processing artifacts)
            A.GaussNoise(var_limit=(15.0, 60.0), mean=0, p=0.5),
            A.MotionBlur(blur_limit=9, p=0.4),
            A.GaussianBlur(blur_limit=5, p=0.3),
            A.MedianBlur(blur_limit=5, p=0.2),
            
            # Spatial and cropping artifacts
            A.RandomResizedCrop(size=(224, 224), scale=(0.6, 1.0), ratio=(0.75, 1.33), p=0.4),
            A.PadIfNeeded(min_height=224, min_width=224, border_mode=cv2.BORDER_CONSTANT, value=0),
            A.Resize(224, 224),
            
            # Additional manipulation artifacts
            A.CLAHE(clip_limit=4.0, tile_grid_size=(8, 8), p=0.3),
            A.RandomGamma(gamma_limit=(50, 150), p=0.3),
        ])
        
        # Minimal processing for real images
        self.real_preprocessing = A.Compose([
            A.Resize(224, 224),
            A.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
            ToTensorV2()
        ])

    def load_comprehensive_datasets(self):
        """Load ALL discovered disaster datasets."""
        print("\n=== LOADING COMPREHENSIVE DISASTER DATASETS ===")
        
        # Define ALL dataset configurations based on our analysis
        dataset_configs = [
            # MAJOR DATASETS - ASONAM17 Damage Dataset (25,820 images)
            {
                'path': self.data_dir / 'ASONAM17_Damage_Image_Dataset' / 'ecuador_eq',
                'disaster_type': 'earthquake',
                'name': 'Ecuador Earthquake (ASONAM17)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'ASONAM17_Damage_Image_Dataset' / 'nepal_eq',
                'disaster_type': 'earthquake',
                'name': 'Nepal Earthquake (ASONAM17)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'ASONAM17_Damage_Image_Dataset' / 'matthew_hurricane',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Matthew (ASONAM17)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'ASONAM17_Damage_Image_Dataset' / 'ruby_typhoon',
                'disaster_type': 'typhoon',
                'name': 'Typhoon Ruby (ASONAM17)',
                'authenticity': 'real'
            },
            
            # MAJOR DATASETS - CrisisMmd (18,126 images)
            {
                'path': self.data_dir / 'crisismmd' / 'data_image' / 'california_wildfires',
                'disaster_type': 'fire',
                'name': 'California Wildfires (CrisisMmd)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'crisismmd' / 'data_image' / 'hurricane_harvey',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Harvey (CrisisMmd)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'crisismmd' / 'data_image' / 'hurricane_irma',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Irma (CrisisMmd)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'crisismmd' / 'data_image' / 'hurricane_maria',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Maria (CrisisMmd)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'crisismmd' / 'data_image' / 'iraq_iran_earthquake',
                'disaster_type': 'earthquake',
                'name': 'Iraq-Iran Earthquake (CrisisMmd)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'crisismmd' / 'data_image' / 'mexico_earthquake',
                'disaster_type': 'earthquake',
                'name': 'Mexico Earthquake (CrisisMmd)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'crisismmd' / 'data_image' / 'srilanka_floods',
                'disaster_type': 'flood',
                'name': 'Sri Lanka Floods (CrisisMmd)',
                'authenticity': 'real'
            },
            
            # AIDR DISASTER DATASETS
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'chennai_flood',
                'disaster_type': 'flood',
                'name': 'Chennai Flood (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'kerala_flood_2018',
                'disaster_type': 'flood',
                'name': 'Kerala Flood 2018 (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'greece_wildfire_2018',
                'disaster_type': 'fire',
                'name': 'Greece Wildfire 2018 (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'earthquake_north_of_chile',
                'disaster_type': 'earthquake',
                'name': 'Chile Earthquake (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'nepal_earthquake',
                'disaster_type': 'earthquake',
                'name': 'Nepal Earthquake (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'terremotoitalia',
                'disaster_type': 'earthquake',
                'name': 'Italy Earthquake (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'harvey',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Harvey (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'irma',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Irma (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'maria',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Maria (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'hurricane_florence_2018',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Florence 2018 (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'hurricane_michael_2018',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Michael 2018 (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_disaster_types' / 'typhoon_mangkhut_2018',
                'disaster_type': 'typhoon',
                'name': 'Typhoon Mangkhut 2018 (AIDR)',
                'authenticity': 'real'
            },
            
            # AIDR INFO DATASETS
            {
                'path': self.data_dir / 'aidr_info' / 'chennai_flood',
                'disaster_type': 'flood',
                'name': 'Chennai Flood Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'kerala_flood_2018',
                'disaster_type': 'flood',
                'name': 'Kerala Flood 2018 Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'greece_wildfire_2018',
                'disaster_type': 'fire',
                'name': 'Greece Wildfire 2018 Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'earthquake_north_of_chile',
                'disaster_type': 'earthquake',
                'name': 'Chile Earthquake Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'nepal_earthquake',
                'disaster_type': 'earthquake',
                'name': 'Nepal Earthquake Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'terremotoitalia',
                'disaster_type': 'earthquake',
                'name': 'Italy Earthquake Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'harvey',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Harvey Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'irma',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Irma Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'maria',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Maria Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'hurricane_florence_2018',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Florence 2018 Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'hurricane_michael_2018',
                'disaster_type': 'typhoon',
                'name': 'Hurricane Michael 2018 Info (AIDR)',
                'authenticity': 'real'
            },
            {
                'path': self.data_dir / 'aidr_info' / 'typhoon_mangkhut_2018',
                'disaster_type': 'typhoon',
                'name': 'Typhoon Mangkhut 2018 Info (AIDR)',
                'authenticity': 'real'
            }
        ]
        
        total_images = 0
        successful_datasets = 0
        
        for config in dataset_configs:
            dataset_path = config['path']
            disaster_type = config['disaster_type']
            dataset_name = config['name']
            authenticity = config['authenticity']
            
            if not dataset_path.exists():
                self.logger.warning(f"Dataset path not found: {dataset_path}")
                continue
            
            print(f"\n📁 Processing: {dataset_name}")
            print(f"   Path: {dataset_path}")
            
            # Count and load images from this dataset
            image_count = 0
            for image_file in dataset_path.rglob('*'):
                if image_file.is_file() and image_file.suffix.lower() in self.valid_extensions:
                    # Validate image
                    if self.validate_image(image_file):
                        self.comprehensive_data.append({
                            'image_path': str(image_file),
                            'relative_path': str(image_file.relative_to(self.data_dir)),
                            'disaster_type': disaster_type,
                            'authenticity': authenticity,
                            'dataset_name': dataset_name,
                            'dataset_source': config['name'].split('(')[1].replace(')', '') if '(' in config['name'] else 'Unknown'
                        })
                        image_count += 1
                        total_images += 1
                        
                        # Progress indicator
                        if image_count % 1000 == 0:
                            print(f"   Processed: {image_count:,} images...")
            
            if image_count > 0:
                self.dataset_stats[disaster_type][authenticity] += image_count
                successful_datasets += 1
                print(f"   ✅ Loaded: {image_count:,} images ({disaster_type}, {authenticity})")
            else:
                print(f"   ❌ No valid images found")
        
        print(f"\n🎉 COMPREHENSIVE DATASET LOADING COMPLETE!")
        print(f"   📊 Total images loaded: {total_images:,}")
        print(f"   📁 Successful datasets: {successful_datasets}/{len(dataset_configs)}")
        
        # Print breakdown by disaster type
        print(f"\n📈 BREAKDOWN BY DISASTER TYPE:")
        for disaster_type in ['fire', 'flood', 'earthquake', 'typhoon']:
            real_count = self.dataset_stats[disaster_type]['real']
            print(f"   🔥 {disaster_type.upper()}: {real_count:,} real images")
        
        return len(self.comprehensive_data)

    def validate_image(self, image_path):
        """Validate image for processing."""
        try:
            # Check file size (skip very large files)
            file_size = image_path.stat().st_size
            if file_size > 20 * 1024 * 1024:  # 20MB limit
                return False
            
            # Validate image can be opened
            with Image.open(image_path) as img:
                img.verify()
            
            # Check dimensions
            with Image.open(image_path) as img:
                width, height = img.size
                
                if width < self.min_size[0] or height < self.min_size[1]:
                    return False
                
                if width > self.max_size[0] or height > self.max_size[1]:
                    return False
                
                # Basic quality check
                img_array = np.array(img.convert('RGB'))
                if np.std(img_array) < 10:  # Very low variance indicates blank/low quality
                    return False
                
                return True
                
        except Exception:
            return False

    def create_fake_variants(self, num_fake_per_real=1):
        """Create fake variants of real disaster images using augmentation."""
        print(f"\n=== CREATING FAKE VARIANTS ===")
        print(f"Target ratio: {num_fake_per_real} fake image(s) per real image")
        
        real_images = [item for item in self.comprehensive_data if item['authenticity'] == 'real']
        fake_variants = []
        
        # Create output directory for fake images
        fake_output_dir = self.output_dir / 'fake_images'
        fake_output_dir.mkdir(exist_ok=True)
        
        total_to_process = len(real_images) * num_fake_per_real
        processed = 0
        
        for real_image in real_images:
            try:
                # Load the real image
                image_path = Path(real_image['image_path'])
                with Image.open(image_path) as img:
                    img_array = np.array(img.convert('RGB'))
                
                # Generate fake variants
                for variant_idx in range(num_fake_per_real):
                    # Apply fake augmentation
                    augmented = self.fake_augmentation(image=img_array)
                    fake_image = augmented['image']
                    
                    # Save fake image
                    fake_filename = f"fake_{image_path.stem}_variant_{variant_idx}.jpg"
                    fake_path = fake_output_dir / real_image['disaster_type'] / fake_filename
                    fake_path.parent.mkdir(parents=True, exist_ok=True)
                    
                    fake_pil = Image.fromarray(fake_image)
                    fake_pil.save(fake_path, 'JPEG', quality=85)
                    
                    # Add to fake variants list
                    fake_variants.append({
                        'image_path': str(fake_path),
                        'relative_path': str(fake_path.relative_to(self.output_dir)),
                        'disaster_type': real_image['disaster_type'],
                        'authenticity': 'fake',
                        'dataset_name': f"Fake variant of {real_image['dataset_name']}",
                        'dataset_source': 'Generated',
                        'source_image': real_image['image_path']
                    })
                    
                    processed += 1
                    
                    # Progress indicator
                    if processed % 1000 == 0:
                        progress = (processed / total_to_process) * 100
                        print(f"   Generated: {processed:,}/{total_to_process:,} fake images ({progress:.1f}%)")
            
            except Exception as e:
                self.logger.warning(f"Failed to create fake variant for {real_image['image_path']}: {e}")
                continue
        
        # Add fake variants to comprehensive data
        self.comprehensive_data.extend(fake_variants)
        
        # Update stats
        for variant in fake_variants:
            self.dataset_stats[variant['disaster_type']]['fake'] += 1
        
        print(f"✅ Created {len(fake_variants):,} fake variants")
        return len(fake_variants)

    def create_balanced_splits(self, train_ratio=0.7, val_ratio=0.15, test_ratio=0.15):
        """Create balanced train/val/test splits."""
        print(f"\n=== CREATING BALANCED DATASET SPLITS ===")
        
        # Convert to DataFrame for easier manipulation
        df = pd.DataFrame(self.comprehensive_data)
        
        print(f"Total dataset size: {len(df):,} images")
        print(f"Split ratios - Train: {train_ratio}, Val: {val_ratio}, Test: {test_ratio}")
        
        # Split by disaster type and authenticity to ensure balance
        train_data, val_data, test_data = [], [], []
        
        for disaster_type in ['fire', 'flood', 'earthquake', 'typhoon']:
            for authenticity in ['real', 'fake']:
                subset = df[(df['disaster_type'] == disaster_type) & (df['authenticity'] == authenticity)]
                
                if len(subset) == 0:
                    continue
                
                # Shuffle the subset
                subset = subset.sample(frac=1, random_state=42).reset_index(drop=True)
                
                # Calculate split sizes
                n_total = len(subset)
                n_train = int(n_total * train_ratio)
                n_val = int(n_total * val_ratio)
                n_test = n_total - n_train - n_val
                
                # Split the data
                train_subset = subset.iloc[:n_train]
                val_subset = subset.iloc[n_train:n_train + n_val]
                test_subset = subset.iloc[n_train + n_val:]
                
                train_data.append(train_subset)
                val_data.append(val_subset)
                test_data.append(test_subset)
                
                print(f"   {disaster_type} ({authenticity}): {n_train} train, {n_val} val, {n_test} test")
        
        # Combine and shuffle splits
        train_df = pd.concat(train_data, ignore_index=True).sample(frac=1, random_state=42)
        val_df = pd.concat(val_data, ignore_index=True).sample(frac=1, random_state=42)
        test_df = pd.concat(test_data, ignore_index=True).sample(frac=1, random_state=42)
        
        print(f"\nFinal split sizes:")
        print(f"   📚 Train: {len(train_df):,} images")
        print(f"   📊 Validation: {len(val_df):,} images")
        print(f"   🧪 Test: {len(test_df):,} images")
        
        return train_df, val_df, test_df

    def save_datasets(self, train_df, val_df, test_df):
        """Save the processed datasets."""
        print(f"\n=== SAVING PROCESSED DATASETS ===")
        
        # Save CSV files
        train_df.to_csv(self.output_dir / 'train_dataset.csv', index=False)
        val_df.to_csv(self.output_dir / 'val_dataset.csv', index=False)
        test_df.to_csv(self.output_dir / 'test_dataset.csv', index=False)
        
        # Save configuration
        config = {
            'total_images': len(train_df) + len(val_df) + len(test_df),
            'train_size': len(train_df),
            'val_size': len(val_df),
            'test_size': len(test_df),
            'disaster_types': ['fire', 'flood', 'earthquake', 'typhoon'],
            'authenticity_classes': ['real', 'fake'],
            'image_size': self.target_size,
            'dataset_stats': dict(self.dataset_stats),
            'created_date': pd.Timestamp.now().isoformat()
        }
        
        with open(self.output_dir / 'model_config.json', 'w') as f:
            json.dump(config, f, indent=2, default=str)
        
        print(f"✅ Datasets saved to: {self.output_dir}")
        print(f"   📄 train_dataset.csv: {len(train_df):,} samples")
        print(f"   📄 val_dataset.csv: {len(val_df):,} samples")
        print(f"   📄 test_dataset.csv: {len(test_df):,} samples")
        print(f"   ⚙️ model_config.json: Configuration saved")

    def run_comprehensive_pipeline(self):
        """Run the complete comprehensive pipeline."""
        print("🚀 STARTING COMPREHENSIVE DISASTER AUTHENTICITY PIPELINE")
        print("=" * 70)
        
        # Step 1: Load all comprehensive datasets
        total_real_images = self.load_comprehensive_datasets()
        
        if total_real_images == 0:
            raise Exception("No images loaded from datasets!")
        
        # Step 2: Create fake variants
        total_fake_images = self.create_fake_variants(num_fake_per_real=1)
        
        # Step 3: Create balanced splits
        train_df, val_df, test_df = self.create_balanced_splits()
        
        # Step 4: Save datasets
        self.save_datasets(train_df, val_df, test_df)
        
        print("\n" + "=" * 70)
        print("🎉 COMPREHENSIVE PIPELINE COMPLETE!")
        print(f"📊 Total processed: {total_real_images + total_fake_images:,} images")
        print(f"✅ Ready for DisasterLink ML training and deployment")
        print("=" * 70)
        
        return train_df, val_df, test_df

if __name__ == "__main__":
    try:
        print("Initializing Comprehensive Disaster Authenticity Pipeline...")
        pipeline = ComprehensiveDisasterAuthenticityPipeline()
        
        # Run the complete pipeline
        train_df, val_df, test_df = pipeline.run_comprehensive_pipeline()
        
        print("\n🎯 NEXT STEPS:")
        print("1. Run disaster_model_trainer.py to train the model")
        print("2. Use disaster_api.py to deploy the trained model")
        print("3. Integrate with DisasterLink using DisasterMLService.php")
        
    except Exception as e:
        print(f"❌ Pipeline failed: {e}")
        import traceback
        traceback.print_exc()
