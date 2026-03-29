# Use the official PHP with Apache image
FROM php:8.2-apache

# Install system dependencies and enable mod_rewrite
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Configure Apache to use the 'public' folder as the root (adjust if your entry point is different)
# Since your index.php is in the root, we'll set the document root to /var/www/html
# If you move files to a 'public' folder, change the line below to: /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html|g' /etc/apache2/sites-available/000-default.conf

# Set the working directory
WORKDIR /var/www/html

# Copy all your project files into the container
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]