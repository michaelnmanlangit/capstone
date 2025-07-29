"""
DisasterLink - Real vs Fake Disaster Image Classification Model
Using pre-trained models optimized for web deployment
"""

import torch
import torch.nn as nn
import torch.optim as optim
from torch.utils.data import Dataset, DataLoader
import torchvision.transforms as transforms
import torchvision.models as models
from PIL import Image
import pandas as pd
import numpy as np
from pathlib import Path
import json
from sklearn.metrics import classification_report, confusion_matrix
import matplotlib.pyplot as plt
import seaborn as sns
from tqdm import tqdm
import warnings
warnings.filterwarnings('ignore')

class DisasterDataset(Dataset):
    def __init__(self, dataframe, root_dir=None, transform=None):
        self.dataframe = dataframe.reset_index(drop=True)
        self.root_dir = Path(root_dir) if root_dir else None
        self.transform = transform
        
        # Print dataset info
        print(f"Dataset loaded: {len(self.dataframe)} samples")
        if 'authenticity' in self.dataframe.columns:
            print(f"Class distribution: \n{self.dataframe['authenticity'].value_counts()}")
        
    def __len__(self):
        return len(self.dataframe)
    
    def __getitem__(self, idx):
        # Get image path and label from CSV structure
        img_path = Path(self.dataframe.iloc[idx]['image_path'])
        authenticity = self.dataframe.iloc[idx]['authenticity']
        
        # Convert authenticity to label: 'real' = 1, 'fake' = 0
        label = 1 if authenticity == 'real' else 0
        
        # Load image
        try:
            image = Image.open(img_path).convert('RGB')
        except Exception as e:
            print(f"Warning: Could not load image {img_path}: {e}")
            # Return a blank image if loading fails
            image = Image.new('RGB', (224, 224), color='white')
        
        # Apply transforms
        if self.transform:
            image = self.transform(image)
            
        return image, torch.tensor(label, dtype=torch.long)

class DisasterAuthenticityModel(nn.Module):
    """
    ResNet50-based model for disaster image authenticity classification
    Optimized for web deployment with good accuracy/speed balance
    """
    def __init__(self, num_classes=2, pretrained=True):
        super(DisasterAuthenticityModel, self).__init__()
        
        # Load pre-trained ResNet50
        self.backbone = models.resnet50(pretrained=pretrained)
        
        # Replace final classifier
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

class EfficientNetModel(nn.Module):
    """
    Alternative EfficientNet model for better accuracy
    """
    def __init__(self, num_classes=2):
        super(EfficientNetModel, self).__init__()
        
        # For this example, we'll use ResNet as EfficientNet requires additional installation
        self.backbone = models.efficientnet_b0(pretrained=True)
        self.backbone.classifier = nn.Sequential(
            nn.Dropout(0.4),
            nn.Linear(1280, 256),
            nn.ReLU(),
            nn.Dropout(0.2),
            nn.Linear(256, num_classes)
        )
        
    def forward(self, x):
        return self.backbone(x)

class DisasterModelTrainer:
    def __init__(self, model_type='resnet50', device=None):
        self.device = device or torch.device('cuda' if torch.cuda.is_available() else 'cpu')
        self.model_type = model_type
        self.training_history = {'train_loss': [], 'val_loss': [], 'train_acc': [], 'val_acc': []}
        
        print(f"🖥️  Using device: {self.device}")
        print(f"🤖 Model type: {model_type}")
        
        # Create model
        if model_type == 'resnet50':
            self.model = DisasterAuthenticityModel()
        elif model_type == 'efficientnet':
            try:
                self.model = EfficientNetModel()
            except:
                print("⚠️  EfficientNet not available, using ResNet50")
                self.model = DisasterAuthenticityModel()
                
        self.model.to(self.device)
        
        # Count parameters
        total_params = sum(p.numel() for p in self.model.parameters())
        trainable_params = sum(p.numel() for p in self.model.parameters() if p.requires_grad)
        print(f"📊 Total parameters: {total_params:,}")
        print(f"🎯 Trainable parameters: {trainable_params:,}")
        
        # Define transforms with enhanced augmentation
        self.train_transform = transforms.Compose([
            transforms.Resize((256, 256)),
            transforms.RandomCrop(224),
            transforms.RandomHorizontalFlip(p=0.5),
            transforms.RandomRotation(degrees=15),
            transforms.ColorJitter(brightness=0.2, contrast=0.2, saturation=0.2, hue=0.1),
            transforms.RandomGrayscale(p=0.1),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
        ])
        
        self.val_transform = transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
        ])
        
        # Create model
        if model_type == 'resnet50':
            self.model = DisasterAuthenticityModel()
        elif model_type == 'efficientnet':
            try:
                self.model = EfficientNetModel()
            except:
                print("EfficientNet not available, using ResNet50")
                self.model = DisasterAuthenticityModel()
                
        self.model.to(self.device)
        
        # Define transforms
        self.train_transform = transforms.Compose([
            transforms.Resize((256, 256)),
            transforms.RandomCrop(224),
            transforms.RandomHorizontalFlip(),
            transforms.RandomRotation(10),
            transforms.ColorJitter(brightness=0.2, contrast=0.2, saturation=0.2, hue=0.1),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
        ])
        
        self.val_transform = transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
        ])
        
    def load_datasets(self, data_dir):
        """Load train, validation, and test datasets"""
        data_path = Path(data_dir)
        
        # Load CSV files
        train_df = pd.read_csv(data_path / "train_dataset.csv")
        val_df = pd.read_csv(data_path / "val_dataset.csv")
        test_df = pd.read_csv(data_path / "test_dataset.csv")
        
        print(f"📊 Loaded CSV files successfully")
        print(f"   Train: {len(train_df)} samples")
        print(f"   Val: {len(val_df)} samples") 
        print(f"   Test: {len(test_df)} samples")
        
        # Create datasets (no need to pass root_dir since paths are absolute)
        train_dataset = DisasterDataset(train_df, transform=self.train_transform)
        val_dataset = DisasterDataset(val_df, transform=self.val_transform)
        test_dataset = DisasterDataset(test_df, transform=self.val_transform)
        
        # Create data loaders with reduced num_workers for Windows
        self.train_loader = DataLoader(train_dataset, batch_size=32, shuffle=True, num_workers=0)
        self.val_loader = DataLoader(val_dataset, batch_size=32, shuffle=False, num_workers=0)
        self.test_loader = DataLoader(test_dataset, batch_size=32, shuffle=False, num_workers=0)
        
        print(f"✅ Data loaders created successfully")
        print(f"   Training batches: {len(self.train_loader)}")
        print(f"   Validation batches: {len(self.val_loader)}")
        print(f"   Test batches: {len(self.test_loader)}")
        
        return train_dataset, val_dataset, test_dataset
    
    def train_epoch(self, epoch):
        """Train for one epoch"""
        self.model.train()
        running_loss = 0.0
        correct = 0
        total = 0
        
        pbar = tqdm(self.train_loader, desc=f'Epoch {epoch+1} Training')
        
        for batch_idx, (data, target) in enumerate(pbar):
            data, target = data.to(self.device), target.to(self.device)
            
            self.optimizer.zero_grad()
            output = self.model(data)
            loss = self.criterion(output, target)
            loss.backward()
            self.optimizer.step()
            
            running_loss += loss.item()
            pred = output.argmax(dim=1, keepdim=True)
            correct += pred.eq(target.view_as(pred)).sum().item()
            total += target.size(0)
            
            # Update progress bar
            acc = 100. * correct / total
            pbar.set_postfix({'Loss': f'{running_loss/(batch_idx+1):.4f}', 'Acc': f'{acc:.2f}%'})
        
        return running_loss / len(self.train_loader), 100. * correct / total
    
    def validate(self):
        """Validate the model"""
        self.model.eval()
        val_loss = 0
        correct = 0
        total = 0
        
        with torch.no_grad():
            for data, target in self.val_loader:
                data, target = data.to(self.device), target.to(self.device)
                output = self.model(data)
                val_loss += self.criterion(output, target).item()
                pred = output.argmax(dim=1, keepdim=True)
                correct += pred.eq(target.view_as(pred)).sum().item()
                total += target.size(0)
        
        val_loss /= len(self.val_loader)
        val_acc = 100. * correct / total
        
        return val_loss, val_acc
    
    def train_model(self, num_epochs=20, learning_rate=0.001):
        """Train the model"""
        
        # Setup optimizer and loss
        self.optimizer = optim.Adam(self.model.parameters(), lr=learning_rate, weight_decay=1e-4)
        self.scheduler = optim.lr_scheduler.ReduceLROnPlateau(self.optimizer, 'min', patience=3, factor=0.5)
        self.criterion = nn.CrossEntropyLoss()
        
        # Training history
        train_losses, train_accs = [], []
        val_losses, val_accs = [], []
        best_val_acc = 0
        
        print(f"Training {self.model_type} model on {self.device}")
        print(f"Total parameters: {sum(p.numel() for p in self.model.parameters()):,}")
        
        for epoch in range(num_epochs):
            # Train
            train_loss, train_acc = self.train_epoch(epoch)
            
            # Validate
            val_loss, val_acc = self.validate()
            
            # Update scheduler
            self.scheduler.step(val_loss)
            
            # Save history
            train_losses.append(train_loss)
            train_accs.append(train_acc)
            val_losses.append(val_loss)
            val_accs.append(val_acc)
            
            print(f'Epoch {epoch+1}: Train Loss: {train_loss:.4f}, Train Acc: {train_acc:.2f}%, '
                  f'Val Loss: {val_loss:.4f}, Val Acc: {val_acc:.2f}%')
            
            # Save best model
            if val_acc > best_val_acc:
                best_val_acc = val_acc
                torch.save({
                    'epoch': epoch,
                    'model_state_dict': self.model.state_dict(),
                    'optimizer_state_dict': self.optimizer.state_dict(),
                    'val_acc': val_acc,
                    'model_type': self.model_type
                }, 'best_disaster_model.pth')
                print(f'New best model saved with validation accuracy: {val_acc:.2f}%')
        
        return {
            'train_losses': train_losses,
            'train_accs': train_accs,
            'val_losses': val_losses,
            'val_accs': val_accs,
            'best_val_acc': best_val_acc
        }
    
    def evaluate_model(self):
        """Evaluate model on test set"""
        self.model.eval()
        all_preds = []
        all_targets = []
        
        with torch.no_grad():
            for data, target in tqdm(self.test_loader, desc='Testing'):
                data, target = data.to(self.device), target.to(self.device)
                output = self.model(data)
                pred = output.argmax(dim=1)
                
                all_preds.extend(pred.cpu().numpy())
                all_targets.extend(target.cpu().numpy())
        
        # Calculate metrics
        test_acc = (np.array(all_preds) == np.array(all_targets)).mean() * 100
        
        # Classification report
        class_names = ['Fake', 'Real']
        report = classification_report(all_targets, all_preds, target_names=class_names, output_dict=True)
        
        print(f"\nTest Accuracy: {test_acc:.2f}%")
        print("\nClassification Report:")
        print(classification_report(all_targets, all_preds, target_names=class_names))
        
        # Confusion Matrix
        cm = confusion_matrix(all_targets, all_preds)
        plt.figure(figsize=(8, 6))
        sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', xticklabels=class_names, yticklabels=class_names)
        plt.title('Confusion Matrix - Disaster Image Authenticity')
        plt.ylabel('True Label')
        plt.xlabel('Predicted Label')
        plt.savefig('confusion_matrix.png', dpi=300, bbox_inches='tight')
        plt.show()
        
        return test_acc, report
    
    def save_web_model(self, model_path='disaster_authenticity_model.pth'):
        """Save model for web deployment"""
        
        # Load best model
        checkpoint = torch.load('best_disaster_model.pth', map_location=self.device)
        self.model.load_state_dict(checkpoint['model_state_dict'])
        
        # Save for inference
        torch.save({
            'model_state_dict': self.model.state_dict(),
            'model_type': self.model_type,
            'input_size': [224, 224],
            'classes': ['fake', 'real'],
            'transforms': {
                'resize': [224, 224],
                'normalize': {
                    'mean': [0.485, 0.456, 0.406],
                    'std': [0.229, 0.224, 0.225]
                }
            }
        }, model_path)
        
        print(f"Web deployment model saved as: {model_path}")
        
        # Save model configuration for web app
        config = {
            "model_info": {
                "type": self.model_type,
                "task": "disaster_authenticity_classification",
                "classes": ["fake", "real"],
                "confidence_threshold": 0.7,
                "input_size": [224, 224, 3]
            },
            "preprocessing": {
                "resize": [224, 224],
                "normalize": {
                    "mean": [0.485, 0.456, 0.406],
                    "std": [0.229, 0.224, 0.225]
                }
            },
            "deployment": {
                "framework": "pytorch",
                "model_file": model_path,
                "batch_size": 1,
                "max_image_size": "10MB"
            }
        }
        
        with open('model_config_web.json', 'w') as f:
            json.dump(config, f, indent=2)
        
        return config

def main():
    """Main training pipeline"""
    
    # Initialize trainer
    trainer = DisasterModelTrainer(model_type='resnet50')
    
    # Load datasets
    print("Loading datasets...")
    trainer.load_datasets('disaster_authenticity_dataset')
    
    # Train model
    print("Starting training...")
    history = trainer.train_model(num_epochs=15, learning_rate=0.001)
    
    # Evaluate model
    print("Evaluating model...")
    test_acc, report = trainer.evaluate_model()
    
    # Save for web deployment
    print("Preparing for web deployment...")
    config = trainer.save_web_model()
    
    print(f"\n=== MODEL READY FOR DISASTERLINK ===")
    print(f"Best validation accuracy: {history['best_val_acc']:.2f}%")
    print(f"Test accuracy: {test_acc:.2f}%")
    print(f"Model saved for web deployment")
    print(f"Configuration saved for integration")
    
    return trainer, history, config

if __name__ == "__main__":
    main()
