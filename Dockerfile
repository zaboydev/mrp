FROM php:5.6-apache

# Set environment variables for non-interactive installation
ENV DEBIAN_FRONTEND=noninteractive

# Update source list to use Debian archive for old packages
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list && \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until

RUN a2enmod rewrite
# Set ServerName to avoid warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf


# Install necessary packages and PHP extensions
# RUN apt-get update && \
#     apt-get install -y \
#         nano \
#         libpng-dev \
#         libjpeg-dev \
#         libfreetype6-dev \
#         libmcrypt-dev \
#         libpq-dev \
#         && docker-php-ext-install \
#             pdo \
#             pdo_pgsql \
#             pgsql \
#             gd \
#             mysqli \
#         && apt-get clean && \
#         rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    nano \
    libbz2-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libonig-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libldap2-dev \
    libsqlite3-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install bcmath bz2 calendar curl ctype intl mbstring mysqli pdo pdo_mysql soap xmlrpc zip gd \
    && docker-php-ext-enable opcache \
    && apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Set the DocumentRoot for Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Update Apache configuration to allow .htaccess files
RUN echo "<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/override.conf && \
    a2enconf override

# Add DirectoryIndex for Apache to look for index.php first
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# Copy your application files into the container
COPY ./ /var/www/html

# Set permissions for the application directory
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Run Apache in the foreground
CMD ["apache2-foreground"]
