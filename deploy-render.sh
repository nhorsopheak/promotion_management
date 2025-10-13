#!/bin/bash

# Kravanh-POS Render Deployment Script
# Run this to prepare for Render deployment

echo "🚀 Kravanh-POS Render Deployment Script"
echo "======================================="

# Check if git is clean
if [[ -n $(git status --porcelain) ]]; then
    echo "❌ Git working directory is not clean. Please commit your changes first."
    exit 1
fi

echo "✅ Git working directory is clean"

# Push to main branch
echo "📤 Pushing to GitHub..."
git push origin main

if [[ $? -eq 0 ]]; then
    echo "✅ Successfully pushed to GitHub"
    echo ""
    echo "🌐 Next steps for Render deployment:"
    echo "1. Go to https://render.com"
    echo "2. Create account and connect GitHub"
    echo "3. Create New → Web Service"
    echo "4. Select your repository: nhorsopheak/promotion_management"
    echo "5. Runtime: Docker"
    echo "6. Dockerfile Path: ./Dockerfile.render"
    echo "7. Deploy!"
    echo ""
    echo "🔗 Your app will be live at: https://your-app-name.onrender.com"
    echo ""
    echo "📧 Admin login: admin@example.com / password"
else
    echo "❌ Failed to push to GitHub"
    exit 1
fi
