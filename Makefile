.PHONY: test test-all install update clean dev bower load assets optimize

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test       to run unit tests only"
	@echo "  test-all   to run all tests"
	@echo "  install    to make a Composer install"
	@echo "  update     to make a Composer update then a Bower update"
#	@echo "  doc        to generate documentation for a bundle"
	@echo "  clean      to remove and warmup cache"
	@echo "  dev        to start Built-in web server of PHP"
	@echo "  bower      to make a Bower install"
	@echo "  load       to load fixtures"
	@echo "  assets     to install assets"
	@echo "  optimize   to optimize sandbox"
	@echo "  check      run default symfony check"

test:
	phpunit -c app

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
	/usr/local/node/node-v0.10.22/bin/bower install

update:
	composer update
	/usr/local/node/node-v0.10.22/bin/bower update

#doc:
#	cd docs && sphinx-build -nW -b html -d _build/doctrees . _build/html

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

