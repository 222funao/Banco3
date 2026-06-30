#!/bin/sh
set -eu

APP_PORT="${PORT:-8080}"

sed -ri "s!^Listen 80\$!Listen ${APP_PORT}!" /etc/apache2/ports.conf
sed -ri "s!<VirtualHost \\*:80>!<VirtualHost *:${APP_PORT}>!" \
    /etc/apache2/sites-available/000-default.conf

echo "Banco 3: starting Apache on 0.0.0.0:${APP_PORT}"
php -r 'echo "Banco 3: pdo_pgsql=" . (extension_loaded("pdo_pgsql") ? "enabled" : "missing") . PHP_EOL;'
apache2ctl configtest

exec apache2-foreground
