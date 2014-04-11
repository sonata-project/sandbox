Sonata Standard Edition
=======================

What's inside?
--------------

Sonata Standard Edition comes pre-configured with the following bundles:

* Bundles from Symfony Standard distribution
* Sonata Admin Bundles: Admin and Doctrine ORM Admin
* Sonata Ecommerce Bundles: Payment, Customer, Invoice, Order and Product
* Sonata Foundation Bundles: Core, Notification, Formatter, Intl, Cache, Seo and Easy Extends
* Sonata Feature Bundles: Page, Media, News, User, Block, Timeline
* Api Bundles: FOSRestBundle, BazingaHateoasBundle, NelmioApiDocBundle and JMSSerializerBundle

Installation
------------

Get composer:

    curl -s http://getcomposer.org/installer | php

Run the following command for the 2.3 branch:

    php composer.phar create-project sonata-project/sandbox:2.3.x-dev
    
Or to get the 2.3 develop branch:

    php composer.phar create-project sonata-project/sandbox:dev-2.3-develop

The installation process used Incenteev's ParameterHandler to handle parameters.yml configuration. With the current
installation, it is possible to use environment variables to configure this file:

    DATABASE_NAME=sonata DATABASE_USER=root DATABASE_PASSWORD="" php composer.phar create-project sonata-project/sandbox:dev-2.3-develop

Run
---

If you are running PHP5.4, you can use the built in server to start the demo:

    php -S localhost:9090 -t web/app

Now open your browser and go to http://localhost:9090/

Tests
-----

### Functional testing

To run the Behat tests, copy the default configuration file and adjust the base_url to your needs

    # behat.yml
    imports:
        - behat.yml.dist

    # Overwrite only the config you want to change here

You can now run the tests suite using the following command

    php bin/behat

To get more informations about Behat, feel free to check `the official documentation
<http://docs.behat.org/>`_.


### Unit testing

To run the Sonata test suites, you can run the command:

    bin/qa_client_ci.sh

Enjoy!
