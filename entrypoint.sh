#!/bin/sh

# Ejecutar optimizaciones de Laravel para un mejor rendimiento
echo "Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Opcional: Ejecutar migraciones. Descomentar si la base de datos está lista al iniciar.
# echo "Running database migrations..."
# php artisan migrate --force

# Iniciar PHP-FPM en segundo plano
php-fpm &

# Iniciar Nginx en primer plano para mantener el contenedor en ejecución
nginx -g 'daemon off;'
