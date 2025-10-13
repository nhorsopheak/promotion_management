#!/bin/bash

# Kravanh-POS Railway Deployment Script
# Run this after pushing your code to GitHub

echo "🚀 Kravanh-POS Deployment Script"
echo "================================"

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
    echo "🌐 Next steps:"
    echo "1. Go to https://railway.app"
    echo "2. Create new project from GitHub"
    echo "3. Select your repository"
    echo "4. Railway will auto-detect the Dockerfile"
    echo "5. Set APP_URL environment variable"
    echo "6. Deploy!"
    echo ""
    echo "🔗 Your app will be live at: https://your-app-name.railway.app"
else
    echo "❌ Failed to push to GitHub"
    exit 1
fi
