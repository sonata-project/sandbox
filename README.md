Sonata Standard Edition
=======================

[ ![Codeship Status for sonata-project/sandbox](https://codeship.com/projects/abaa9ca0-5945-0132-9e3b-069770f0649f/status)](https://codeship.com/projects/50230)

What's inside?
--------------

Sonata Standard Edition comes pre-configured with the following bundles:

* Bundles from Symfony Standard distribution
* Sonata Admin Bundles: Admin and Doctrine ORM Admin
* Sonata Ecommerce Bundles: Payment, Customer, Invoice, Order and Product
* Sonata Foundation Bundles: Core, Notification, Formatter, Intl, Cache, Seo and Easy Extends
* Sonata Feature Bundles: Page, Media, News, User, Block, Timeline
* Api Bundles: FOSRestBundle, BazingaHateoasBundle, NelmioApiDocBundle and JMSSerializerBundle

Quick Installation
------------------

The Sonata Project provides a build of the Sonata Project sandbox to quickly start with the projet.

* Retrieve the code: ``curl -L https://github.com/sonata-project/sandbox-build/archive/2.4.tar.gz | tar xzv``
* Configure default the ``parameters.yml`` file: ``cp app/config/parameters.yml.dist app/config/parameters.yml``
* load the data: ``php bin/load_data.php``
* You should should be ready to go ...

Vagrant Installation
--------------------

The Sonata Project provides a Virtual Machine to help you start the sandbox easier on your own computer. This VM provides you all requirements to run sonata.

** What's inside ? **
    
* nginx
* php5-fpm, php5-curl, php5-gd, php5-intl, php5-cli ......
* composer
* mysql
* git

** Requirements **

* [Vagrant][link_vagrant]
* [VirtualBox][link_virtualbox]

** Setup **

* Retrieve the code: ``curl -L https://github.com/sonata-project/sandbox-build/archive/2.4.tar.gz | tar xzv``
* Configure default the ``parameters.yml`` file: ``cp app/config/parameters.yml.dist app/config/parameters.yml``
* Configure your host ``sudo nano /etc/hosts`` and add this line ``192.168.33.99   sonata.local``
* vagrant up --provision (Vagrant is going to get the environnement, install it for you and load sonata sample data)
* Open your browser [here][link_sonata]

Composer Installation
---------------------

Get composer:

    curl -s http://getcomposer.org/installer | php

Run the following command for the 2.4 develop branch:

    php composer.phar create-project sonata-project/sandbox:2.4.x-dev

The installation process used Incenteev's ParameterHandler to handle parameters.yml configuration. With the current
installation, it is possible to use environment variables to configure this file:

    DATABASE_NAME=sonata DATABASE_USER=root DATABASE_PASSWORD="" php composer.phar create-project sonata-project/sandbox:dev-2.4-develop

You might experience some timeout issues with composer, as the ``create-project`` start different scripts, you can increase the default composer value with the ``COMPOSER_PROCESS_TIMEOUT`` env variable:

    COMPOSER_PROCESS_TIMEOUT=600 php composer.phar create-project sonata-project/sandbox:dev-2.4-develop

Fixtures are automatically loaded on the ``composer create-project`` step. If you'd like to reset your sandbox to the default fixtures (or you had an issue while installing and want to fill in the fixtures manually), you may run:

    php bin/load_data.php

This will completely reset your database.

Run
---

If you are running PHP5.4, you can use the built in server to start the demo:

    app/console server:run localhost:9090

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

    bin/qa_behat.sh

To get more informations about Behat, feel free to check [the official documentation][link_behat].


### Unit testing

To run the Sonata test suites, you can run the command:

    bin/qa_client_ci.sh

Enjoy!

[link_behat]: http://docs.behat.org "the official Behat documentation"
[link_vagrant]: http://www.vagrantup.com/downloads.html "Download Vagrant"
[link_virtualbox]: https://www.virtualbox.org/wiki/Downloads "Download VirtualBox"
[link_sonata]: http://sonata.local "Sonata"
