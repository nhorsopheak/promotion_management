FROM php:8.2-cli

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /app

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create SQLite database file
RUN touch database/database.sqlite

# Set permissions
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache database

# Copy environment file
COPY .env.example .env

# Generate application key
RUN php artisan key:generate

# Run database migrations and seeders
RUN php artisan migrate --force
RUN php artisan db:seed --force

# Build assets
RUN npm install && npm run build

# Expose port 80
EXPOSE 80

# Run the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
