.PHONY: static

DIR := ${CURDIR}
QA_IMAGE := jakzal/phpqa:php8.0-alpine

cs-lint:
	@docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) php-cs-fixer fix --dry-run -vvv

cs-fix:
	@docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) php-cs-fixer fix -vvv

phpstan:
	@docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) phpstan analyze
