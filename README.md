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

Composer Installation
---------------------
Get composer:

    curl -s http://getcomposer.org/installer | php
    
Run the following command for the 3.0 branch:

    composer create-project sonata-project/sandbox:3.0

The installation process used Incenteev's ParameterHandler to handle parameters.yml configuration. With the current
installation, it is possible to use environment variables to configure this file:

    DATABASE_NAME=sonata DATABASE_USER=root DATABASE_PASSWORD="" composer create-project sonata-project/sandbox:3.0

You might experience some timeout issues with composer, as the ``create-project`` start different scripts, you can increase the default composer value with the ``COMPOSER_PROCESS_TIMEOUT`` env variable:

    COMPOSER_PROCESS_TIMEOUT=600 composer create-project sonata-project/sandbox:3.0


Standard Installation
---------------------

 Download project files:

    curl -L github https://github.com/sonata-project/sandbox/archive/3.0.tar.gz | tar xzv
    rm sandbox-3.0 sandbox
    cd sandbox
    
or

    git clone https://github.com/sonata-project/sandbox.git
    cd sandbox
    git checkout 3.0
    rm -r .git
    
Configure default the parameters.yml file: 

    cp app/config/parameters.yml.dist app/config/parameters.yml
        
Download vendors:

    composer update
    
Load fixtures:

    vendor/bin/phing  
    
Reset sandbox
-------------
If you'd like to reset your sandbox to the default fixtures (or you had an issue while installing and want to fill in the fixtures manually), you may run:

    vendor/bin/phing  
    
This will completely reset your database.

Vagrant Installation
--------------------
* vagrant up --provision --provider=virtualbox (Vagrant is going to get the environnement, install it for you and load sonata sample data)
* Configure your host ``sudo nano /etc/hosts`` and add this line ``192.168.33.99   sonata.local``
* Open your browser [here][link_sonata]

Run
---

If you are running PHP >=7.1, you can use the built in server to start the demo:

    bin/console server:run localhost:9090

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
