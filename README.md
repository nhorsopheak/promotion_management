# Kravanh-POS - Promotion Management System

A comprehensive Laravel-based promotion management system built with Filament admin panel. Supports multiple promotion types including Buy X Get Y Free, Step Discounts, Fixed Price Bundles, and Percentage Discounts.

## 🚀 Quick Start (Local Development)

```bash
# Clone the repository
git clone <your-repo-url>
cd promotion-management

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate:fresh --seed

# Build assets
npm run build

# Start development server
composer run dev
```

Visit `http://localhost:8000` for the admin panel.

**Default Login:**
- Email: `admin@example.com`
- Password: `password`

## 🌐 Production Deployment

### Free Hosting with Railway (Recommended)

1. **Push your code to GitHub**
2. **Deploy to Railway:**
   - Go to [railway.app](https://railway.app)
   - Create new project from your GitHub repository
   - Railway auto-detects the Dockerfile
   - Set `APP_URL` environment variable
   - Deploy!

**Live demo will be available at:** `https://your-app-name.railway.app`

### Alternative Free Hosting

- **Railway** ⭐⭐⭐⭐⭐ - Easiest setup, full PHP support, SQLite ready
- **Render** ⭐⭐⭐⭐ - Web service with persistent disk
- **Fly.io** ⭐⭐⭐⭐ - Docker-based deployment
- ~~**Firebase**~~ ❌ - Not suitable for Laravel/PHP applications

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed instructions.

## ✨ Features

- **Admin Panel**: Filament-powered admin interface
- **Promotion Types**:
  - Buy X Get Y Free
  - Step Discounts
  - Fixed Price Bundles
  - Percentage Discounts
- **SQLite Database**: No external database required
- **Responsive Design**: Mobile-friendly interface
- **Sample Data**: Pre-loaded with demo products and promotions

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PercentageDiscountPromotionTest
```

## 📚 Documentation

- [Quick Start Guide](QUICK_START.md)
- [Setup Instructions](SETUP.md)
- [Deployment Guide](DEPLOYMENT.md)
- [Implementation Summary](IMPLEMENTATION_SUMMARY.md)
- [Task Roadmap](TASKS.md)

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Filament 3, TailwindCSS
- **Database**: SQLite (production-ready)
- **Build Tool**: Vite
- **Deployment**: Docker + Railway

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# promotion_management
