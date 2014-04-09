test:
	phpunit -c app

test-all:
	./bin/qa_client_ci.sh
	bin/behat -f progress

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
	app/console cache:warmup

dev:
	php -S localhost:8000 -t web

bower:
	bower install

load:
	php bin/load_data.php

assets:
	if [ ! -f bin/yuicompressor.jar ]; then curl -L https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar > bin/yuicompressor.jar; fi;
	rm -rf web/assetic/*
	app/console assets:install --symlink web
	app/console assetic:dump

.PHONY: test test-all install update clean dev bower load assets

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