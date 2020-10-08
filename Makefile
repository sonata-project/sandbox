.PHONY: test test-all install update clean dev bower load assets optimize build

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test       to run unit tests only"
	@echo "  test-all   to run all tests"
	@echo "  install    to make a Composer install"
	@echo "  update     to make a Composer update then a Bower update"
	@echo "  clean      to remove and warmup cache"
	@echo "  dev        to start Built-in web server of PHP"
	@echo "  bower      to make a Bower install"
	@echo "  load       to load fixtures"
	@echo "  assets     to install assets"
	@echo "  optimize   to optimize sandbox"
	@echo "  check      run default symfony check"
	@echo "  build      push code to the build repository"

test:
	./bin/qa_client_ci.sh

test-all:
	./bin/qa_client_ci.sh
	./bin/qa_behat.sh

check:
	php app/check.php

optimize: composer-optimize clean assets

composer-optimize:
	composer dump-autoload -o

install:
	composer install
	bower install

update:
	composer update
	bower update

clean:
	rm -rf app/cache/*
	php app/console cache:warmup --env=prod --no-debug
	php app/console cache:warmup --env=dev

dev:
	php -S localhost:8000 -t web

bower:
	bower install

load:
	php bin/load_data.php

assets:
	if [ ! -f bin/yuicompressor.jar ]; then curl -L https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar > bin/yuicompressor.jar; fi;
	app/console assets:install --symlink web
	app/console assetic:dump

assets-watch:
	app/console assetic:dump --watch

format:
	php-cs-fixer fix src
	rm -rf app/cache/*
	php-cs-fixer fix app


build:
	rm -rf web/sitemap*xml
	find . -name '*.DS_Store' -type f -delete
	git stash
	app/console assets:install web
	bin/qa_build_git.sh . /home/vagrant/sonata-sandbox-build 2.4 2.4
	git stash pop

all:
	@echo "Please choose a task."
.PHONY: all

lint: lint-composer lint-yaml lint-xml lint-php
.PHONY: lint

lint-composer:
	composer validate
.PHONY: lint-composer

lint-yaml:
	yaml-lint --ignore-non-yaml-files --quiet --exclude vendor .

.PHONY: lint-yaml

lint-xml:
	find . \( -name '*.xml' -or -name '*.xliff' \) \
		-not -path './vendor/*' \
		-not -path './src/Resources/public/vendor/*' \
        -not -path './public/*' \
		| while read xmlFile; \
	do \
		XMLLINT_INDENT='    ' xmllint --encode UTF-8 --format "$$xmlFile"|diff - "$$xmlFile"; \
		if [ $$? -ne 0 ] ;then exit 1; fi; \
	done

.PHONY: lint-xml

lint-php:
	php-cs-fixer fix --ansi --verbose --diff --dry-run
.PHONY: lint-php

cs-fix: cs-fix-php cs-fix-xml
.PHONY: cs-fix

cs-fix-php:
	php-cs-fixer fix --verbose
.PHONY: cs-fix-php

cs-fix-xml:
	find . \( -name '*.xml' -or -name '*.xliff' \) \
		-not -path './vendor/*' \
		-not -path './src/Resources/public/vendor/*' \
        -not -path './public/*' \
		| while read xmlFile; \
	do \
		XMLLINT_INDENT='    ' xmllint --encode UTF-8 --format "$$xmlFile" --output "$$xmlFile"; \
	done
.PHONY: cs-fix-xml

build:
	mkdir $@

test:
ifeq ($(shell php --modules|grep --quiet pcov;echo $$?), 0)
	vendor/bin/simple-phpunit -c phpunit.xml.dist --coverage-clover build/logs/clover.xml
else
	vendor/bin/simple-phpunit -c phpunit.xml.dist
endif
.PHONY: test
