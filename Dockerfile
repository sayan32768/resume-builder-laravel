# # ---------- Stage 1: Frontend build ----------
# FROM node:20-alpine as nodebuild
# WORKDIR /app
# COPY package*.json ./
# RUN npm install
# COPY . .
# RUN npm run build

# # ---------- Stage 2: PHP/Laravel ----------
# FROM php:8.2-fpm-bookworm

# RUN sed -i 's|http://deb.debian.org|https://deb.debian.org|g' /etc/apt/sources.list.d/debian.sources \
#  && sed -i 's|http://security.debian.org|https://security.debian.org|g' /etc/apt/sources.list.d/debian.sources \
#  && apt-get update && apt-get install -y \
#     git unzip zip curl libpq-dev \
#  && docker-php-ext-install pdo pdo_pgsql \
#  && rm -rf /var/lib/apt/lists/*

# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# WORKDIR /var/www
# COPY . .

# RUN rm -rf bootstrap/cache/* && mkdir -p bootstrap/cache

# RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# COPY --from=nodebuild /app/public/build /var/www/public/build

# RUN test -f /var/www/public/build/manifest.json

# RUN chown -R www-data:www-data storage bootstrap/cache

# COPY start.sh /start.sh
# RUN chmod +x /start.sh

# CMD ["/start.sh"]

# ---------- Stage 1: Frontend build ----------
FROM node:20-alpine AS nodebuild
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# ---------- Stage 2: PHP/Laravel ----------
FROM php:8.2-cli-bookworm

# Fix Debian repo + install deps
RUN sed -i 's|http://deb.debian.org|https://deb.debian.org|g' /etc/apt/sources.list.d/debian.sources \
 && sed -i 's|http://security.debian.org|https://security.debian.org|g' /etc/apt/sources.list.d/debian.sources \
 && apt-get update && apt-get install -y \
    git unzip zip curl libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# ✅ Remove cached providers/config (prevents Pail crash)
RUN rm -f bootstrap/cache/*.php bootstrap/cache/*.tmp || true

# Install PHP deps (no dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy built Vite assets
COPY --from=nodebuild /app/public/build /var/www/public/build

# ✅ Fail the build if manifest missing
RUN test -f /var/www/public/build/manifest.json

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8000
CMD ["/start.sh"]
