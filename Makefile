init-db:
    php artisan boom:install
generate-doc-api:
	php artisan l5-swagger:generate api_v1
generate-doc-mobile:
	php artisan l5-swagger:generate mobile_v1
ide-helper:
	php artisan ide-helper:generate && php artisan ide-helper:models -N && php artisan ide-helper:meta
clear-cache:
	php artisan optimize:clear
grumphp:
	./vendor/bin/grumphp run
show-routes:
	php artisan route:list --except-vendor
show-routes-by-name:
	php artisan route:list --except-vendor --name=$(name)
phpstan:
	./vendor/bin/phpstan analyse
phpstan-generate-baseline:
	./vendor/bin/phpstan analyse --generate-baseline
