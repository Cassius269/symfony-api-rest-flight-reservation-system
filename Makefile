.PHONY: deploy
deploy: ## Deployment tasks
	composer install --node-dev --optimize-autoloader 
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migrations
	php bin/console cache:clear --env=prod