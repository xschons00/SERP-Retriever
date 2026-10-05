FROM php:8.2-cli

# Install required system dependencies and PHP extensions for cURL and DOM
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libxml2-dev \
    && docker-php-ext-install curl dom

WORKDIR /app

# Copy current project files into container
COPY . /app

EXPOSE 8080

# Run PHP built-in server bound to all interfaces
CMD ["php", "-S", "0.0.0.0:8080", "-t", "src"]