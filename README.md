Sonata Standard Edition
=======================

What's inside?
--------------

Sonata Standard Edition comes pre-configured with the following bundles:

* Bundles from Symfony Standard distribution
* Sonata Admin Bundles: Admin and Doctrine ORM Admin
* Sonata Ecommerce Bundles: Payment, Customer, Invoice, Order and Product
* Sonata Foundation Bundles: Notification, Formatter, Intl, Cache, Seo and Easy Extends
* Sonata Feature Bundles: Page, Media, News, User, Block, Timeline
* Api Bundles: FOSRestBundle, BazingaHateoasBundle, NelmioApiDocBundle and JMSSerializerBundle

Installation
------------

### Download sandbox files by one of possible examples

Curl:

    curl -L github https://github.com/sonata-project/sandbox-build/archive/master.tar.gz | tar xzv
    cd sandbox

Git:

    git clone https://github.com/sonata-project/sandbox.git
    cd sandbox
    git checkout master
    
### Prepare configuration

* Copy configuration file: ``cp .env .env.local``
* Edit ``.env.local`` to configure own environment

### Load fixtures data 

    vendor/bin/phing
    
* You should be ready to go ...

Vagrant Installation
--------------------

* vagrant up --provision --provider=virtualbox (Vagrant is going to get the environnement, install it for you and load sonata sample data)
* Configure your host ``sudo nano /etc/hosts`` and add this line ``192.168.33.99   sonata.local``
* Open your browser [here][link_sonata]


Run
---

If you are running PHP 7.2 or above, you can use symfony to start the demo:

    symfony server:start --port=9090

Now open your browser and go to http://localhost:9090/

Tests
-----

### Functional testing

To run the Behat tests, copy the default configuration file and adjust the base_url to your needs

* Copy configuration file: ``cp behat.yml.dist behat.yml``
* Edit it ``behat.yml``

You can now run the tests suite by using the following command:

    bin/qa_behat.sh

To get more informations about Behat, feel free to check [the official documentation][link_behat].


### Unit testing

To run the sandbox test suites, you can run the command:

    vendor/bin/simple-phpunit
    
You can also run the whole sonata-project bundles test suites by using the following command:

    bin/qa_client_ci.sh

Enjoy!

[link_behat]: http://docs.behat.org "the official Behat documentation"
[link_vagrant]: http://www.vagrantup.com/downloads.html "Download Vagrant"
[link_virtualbox]: https://www.virtualbox.org/wiki/Downloads "Download VirtualBox"
[link_sonata]: http://sonata.local "Sonata"
