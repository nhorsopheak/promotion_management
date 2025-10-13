# 🚀 Kravanh-POS Deployment Guide

## ⚠️ Railway Trial Expired - Alternative Options

Since your Railway trial has expired, here are **free alternatives** that support Laravel + SQLite:

---

## 🥇 Render (Recommended Alternative)

### Free Tier: 750 hours/month (persistent storage included)

### Step 1: Deploy to Render
1. **Sign up:** Go to [render.com](https://render.com) and create account
2. **Connect GitHub:** Link your GitHub account
3. **Create Web Service:**
   - Click "New" → "Web Service"
   - Connect your `nhorsopheak/promotion_management` repository
   - **Runtime:** `Docker`
   - **Dockerfile Path:** `./Dockerfile.render`
   - Click "Create Web Service"

### Step 2: Configuration
Render will auto-detect your `render.yaml` configuration. The service will:
- ✅ Build with Docker
- ✅ Install dependencies
- ✅ Run migrations and seeders
- ✅ Start Laravel server

### Step 3: Access Your App
- **URL:** `https://your-app-name.onrender.com`
- **Admin Panel:** Same URL (Filament is at root)
- **Credentials:** `admin@example.com` / `password`

---

## 🥈 Fly.io (Alternative)

### Free Tier: Some free credits + pay-as-you-go

### Step 1: Install Fly CLI
```bash
curl -L https://fly.io/install.sh | sh
```

### Step 2: Deploy
```bash
fly launch
# Follow prompts, select Singapore region
fly deploy
```

### Step 3: Access
- **URL:** `https://your-app-name.fly.dev`
- Fly.io auto-configures everything from `fly.toml`

---

## Hosting Platform Comparison (Free Tiers)

| Platform | Free Hours/Month | Storage | SQLite Support | Ease of Setup |
|----------|------------------|---------|----------------|----------------|
| **Render** ⭐⭐⭐⭐⭐ | 750 hours | ✅ Persistent | ✅ Full | ⭐⭐⭐⭐⭐ |
| **Fly.io** ⭐⭐⭐⭐ | Limited credits | ✅ Persistent | ✅ Full | ⭐⭐⭐⭐ |
| **Railway** ❌ | Trial expired | ❌ No free | ❌ No | ❌ N/A |

---

## Quick Deploy Commands

### For Render:
```bash
# Just push to GitHub, then use Render web interface
git add .
git commit -m "Ready for Render deployment"
git push origin main
```

### For Fly.io:
```bash
# Install CLI and deploy
curl -L https://fly.io/install.sh | sh
fly launch
fly deploy
```

---

## Troubleshooting

### Render Issues:
- **Build timeout:** Your app might take >15 minutes to build
- **Memory limit:** Free tier has 512MB RAM limit
- **Cold starts:** First request after inactivity takes ~10-30 seconds

### Fly.io Issues:
- **Region selection:** Choose Singapore (`sin`) for better performance
- **Credits:** Monitor free credits usage

### General Issues:
- **Database:** SQLite file is created during build
- **Assets:** Frontend assets are built automatically
- **Environment:** Production settings are pre-configured

---

**Ready to deploy?** I recommend **Render** as it's the easiest alternative with persistent storage included! 🚀

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
