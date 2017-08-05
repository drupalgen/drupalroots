# ----------------
# Make help script
# ----------------

# Usage:
# Add help text after target name starting with '\#\#'
# A category can be added with @category. Team defaults:
# 	dev-environment
# 	docker
# 	test

# Output colors
GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

# Script
HELP_FUN = \
	%help; \
	while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
	print "usage: make [target]\n\n"; \
	for (sort keys %help) { \
	print "${WHITE}$$_:${RESET}\n"; \
	for (@{$$help{$$_}}) { \
	$$sep = " " x (32 - length $$_->[0]); \
	print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
	}; \
	print "\n"; }

help:
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)

init: ## Initialize the project
	docker-compose -f docker-compose-init.yml up -d --build
	docker-compose exec -T php composer install --working-dir=/var/www/html/drupal
	docker-compose exec php /bin/bash -c "cd /var/www/html/drupal/web; ls -la; drush site-install -y standard --db-url='mysql://drupal:drupal@db/drupal' --site-name='Drupal Roots' --account-name=admin --account-pass=drupaladm1n"
ifdef TRAVIS
	docker-compose exec php /bin/bash -c "chown -Rf 1000:1000 /var/www"
endif
	docker-compose down
	make up
	@make provision

up: ## Start the project containers
	docker-compose -f docker-compose.yml up -d --build

down: ## shut down the project containers
	docker-compose down
	docker-compose ps

clean-data: ## Delete the data volumes
	docker volume rm drupalroots_drupal
	docker volume rm drupalroots_mysql-data

cim: ## Import configuration files
	@echo "Importing configuration..."
	@docker-compose exec php /bin/bash -c "drush @default.dev cim"

cex: ## Export configuration files
	@echo "Exporting configuration..."
	@docker-compose exec php /bin/bash -c "drush @default.dev cex -y"

provision: ## run drush commands provisioning the project
	#@echo "Enabling Drupal Roots master..."
	#docker-compose exec -T php drush @default.dev en drupalroots_master -y
	@echo "Running database updates..."
	@docker-compose exec -T php drush @default.dev updb
	@echo "Running entity updates..."
	@docker-compose exec -T php drush @default.dev entup
	#@echo "Importing configuration..."
	#@docker-compose exec -T php drush @default.dev cim
	@echo "Running reverting features..."
	-docker-compose exec -T php drush @default.dev fra -y
	@echo "Resetting cache..."
	@docker-compose exec -T php drush @default.dev cr

phpcs: ## run code sniffer tests
	docker-compose exec -T php drupal/vendor/bin/phpcs --config-set installed_paths drupal/vendor/drupal/coder/coder_sniffer
	# Drupal 8
	## uncomment the following line and remove the next when the custom theme is created
	##docker-compose exec php /bin/bash -c "drupal/vendor/bin/phpcs --standard=Drupal /var/www/html/drupal/web/modules/custom/*/* /var/www/html/drupal/web/themes/custom/* --ignore=*.css --ignore=*.css,*.min.js,*features.*.inc,*.svg,*.jpg,*.png,*.json,*.woff*,*.ttf,*.md,*.sh --exclude=Drupal.InfoFiles.AutoAddedKeys"
	docker-compose exec php /bin/bash -c "drupal/vendor/bin/phpcs --standard=Drupal /var/www/html/drupal/web/modules/custom/*/* --ignore=*.css --ignore=*.css,*.min.js,*features.*.inc,*.svg,*.jpg,*.png,*.json,*.woff*,*.ttf,*.md,*.sh --exclude=Drupal.InfoFiles.AutoAddedKeys"

behat: ## run behat tests
	docker-compose exec -T php drupal/vendor/bin/behat -c tests/behat.yml --tags=~@failing --colors -f progress

update-tests: ## update behat test environment
	docker-compose exec -T php drupal/vendor/bin/behat -c tests/behat.yml --init
