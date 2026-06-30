#!/bin/sh
set -eu

APP_PORT="${PORT:-8080}"

sed -ri "s!^Listen 80\$!Listen ${APP_PORT}!" /etc/apache2/ports.conf
sed -ri "s!<VirtualHost \\*:80>!<VirtualHost *:${APP_PORT}>!" \
    /etc/apache2/sites-available/000-default.conf

# mod_php requires prefork; ensure no conflicting Apache MPM remains enabled.
rm -f /etc/apache2/mods-enabled/mpm_event.load \
    /etc/apache2/mods-enabled/mpm_event.conf \
    /etc/apache2/mods-enabled/mpm_worker.load \
    /etc/apache2/mods-enabled/mpm_worker.conf
a2enmod mpm_prefork >/dev/null

echo "Banco 3: starting Apache on 0.0.0.0:${APP_PORT}"
php -r 'echo "Banco 3: pdo_pgsql=" . (extension_loaded("pdo_pgsql") ? "enabled" : "missing") . PHP_EOL;'
apache2ctl configtest

exec apache2-foreground
