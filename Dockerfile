# Use the official PHP image with Composer and Node.js pre-installed
FROM laravelsail/php82-composer:latest

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update \
    && apt-get install -y \
    sqlite3 libsqlite3-dev curl gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy application files to the container
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
RUN npm install \
    && npm run build

# Ensure storage and cache directories are writable
RUN chmod -R 777 storage bootstrap/cache

# Expose port 8000 for Laravel's development server
EXPOSE 8000

# Start Laravel server by default
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
