composer install --classmap-authoritative

bin/console doctrine:database:create --no-interaction --if-not-exists
bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
bin/console importmap:install
bin/console asset-map:compile

php-fpm -F