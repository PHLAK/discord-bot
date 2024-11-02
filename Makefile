all: dev env # Install and build dependencies and bring up the dev environment

dev development: # Install and build application developemnt dependencies
	@composer install --no-interaction

prod production: # Install and build application production dependencies
	@composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

env environment: # Bring up the development environment
	@docker-compose up -d && php artisan migrate:fresh --seed

update upgrade: # Update application dependencies
	@composer update

outdated: # Check for outdated PHP and JavaScript dependencies
	@composer outdated --direct

tunnel: # Expose the application via secure tunnel
	@composer exec expose share plex-bot.local

