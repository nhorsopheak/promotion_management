# 🚀 Kravanh-POS Deployment Guide

## Railway Free Hosting Deployment

### Prerequisites
- GitHub account
- Railway account (free at https://railway.app)

### Step 1: Prepare Your Codebase

1. **Push to GitHub**
   ```bash
   git add .
   git commit -m "Ready for deployment"
   git push origin main
   ```

### Step 2: Deploy to Railway

1. **Connect to Railway**
   - Go to [railway.app](https://railway.app)
   - Sign up/login with your GitHub account
   - Click "New Project"
   - Choose "Deploy from GitHub repo"
   - Search and select your repository

2. **Configure Environment**
   - Railway will automatically detect your Dockerfile
   - Set environment variables in Railway dashboard:
     ```
     APP_URL=https://your-app-name.railway.app
     APP_KEY=base64:y7c9dmrOkBWKnFWa4ys5aPjSJPpuX8OZnixYCu8cx5Y=
     ```

3. **Deploy**
   - Click "Deploy"
   - Wait for build to complete (usually 5-10 minutes)
   - Your app will be live at `https://your-app-name.railway.app`

### Step 3: Access Your Application

- **Admin Panel**: `https://your-app-name.railway.app`
- **Login Credentials**:
  - Email: `admin@example.com`
  - Password: `password`

### Features Included
- ✅ SQLite database (no external DB needed)
- ✅ File-based sessions and cache
- ✅ Production-optimized settings
- ✅ Admin panel with promotion management
- ✅ Sample data pre-loaded

### Free Tier Limits
- 512 MB RAM
- 1 GB storage
- 1 CPU
- Suitable for development/demo purposes

### Troubleshooting

**Build fails?**
- Check Railway build logs
- Ensure all dependencies are in `composer.json`
- Verify PHP extensions in Dockerfile

**App not loading?**
- Check Railway service logs
- Verify APP_URL in environment variables
- Ensure database migrations ran successfully

**Need more resources?**
- Upgrade to Railway Pro ($5/month)
- Or deploy to Render, Fly.io, or other platforms

---

## Hosting Platform Comparison

| Platform | Free Tier | PHP Support | SQLite Support | Ease of Use | Recommended |
|----------|-----------|-------------|----------------|-------------|-------------|
| **Railway** | ✅ 512MB RAM, 1GB storage | ✅ Full | ✅ Yes | ⭐⭐⭐⭐⭐ | ✅ Yes |
| **Render** | ✅ 750 hours/month | ✅ Full | ✅ Yes | ⭐⭐⭐⭐ | ✅ Yes |
| **Fly.io** | ✅ Free tier available | ✅ Full | ✅ Yes | ⭐⭐⭐⭐ | ✅ Yes |
| **Firebase** | ❌ No free PHP hosting | ⚠️ Limited | ⚠️ Complex | ⭐⭐ | ❌ No |
| **Heroku** | ❌ Requires PostgreSQL | ✅ Full | ❌ No | ⭐⭐⭐ | ❌ No |

---

## Firebase App Hosting (Experimental)

**⚠️ Not Recommended for Laravel**

Firebase App Hosting is in beta and primarily supports Node.js frameworks. For PHP/Laravel:

### Option 1: Firebase Functions (Complex)
1. Rewrite your Laravel app as serverless functions
2. Use Firebase Hosting for frontend
3. **Not practical** for full Laravel applications

### Option 2: Firebase App Hosting (Limited)
- Currently in beta
- Limited PHP support
- Not recommended for production Laravel apps

---

## Alternative Free Hosting Options

### Render (Alternative)
1. Go to [render.com](https://render.com)
2. Create Web Service from Git
3. Select your repository
4. Use this build command: `composer install && npm install && npm run build && php artisan migrate --force && php artisan db:seed --force`
5. Start command: `php artisan serve --host=0.0.0.0 --port=$PORT`

### Fly.io (Alternative)
1. Install Fly CLI: `curl -L https://fly.io/install.sh | sh`
2. Run: `fly launch`
3. Follow prompts to create `fly.toml`
4. Deploy: `fly deploy`

---

**Need help?** Check the Railway documentation or create an issue in your repository.
