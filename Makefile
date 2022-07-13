PHP_CS_FIXER  = ./vendor/bin/php-cs-fixer
PHPUNIT       = ./bin/phpunit

## —— The Makefile ———————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

test: phpunit.xml.dist ## Run main functional and unit tests
	$(eval testsuite ?= 'main') # or "external"
	$(eval filter ?= '.')
	$(PHPUNIT) --testsuite=$(testsuite) --filter=$(filter) --stop-on-failure

coverage: phpunit.xml.dist ## Run main functional and unit tests
	$(eval testsuite ?= 'main') # or "external"
	$(eval filter ?= '.')
	XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html=./tests/coverage --testsuite=$(testsuite) --filter=$(filter) --stop-on-failure

test-all: phpunit.xml.dist ## Run all tests
	$(PHPUNIT) --stop-on-failure

lint-php: ## Lint files with php-cs-fixer
	$(PHP_CS_FIXER) fix --dry-run

fix-php: ## Fix files with php-cs-fixer
	$(PHP_CS_FIXER) fix
