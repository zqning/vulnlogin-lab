FROM php:8.1-apache

# 复制源码到 Web 根目录
COPY ./src/ /var/www/html/

# 设置权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 启用 rewrite（备用）
RUN a2enmod rewrite

EXPOSE 80