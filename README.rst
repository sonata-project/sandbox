Sonata Standard Edition
=======================

What's inside?
--------------

Sonata Standard Edition comes pre-configured with the following bundles:

* Bundles from Symfony Standard distribution
* Sonata Ecommerce - Sonata E-commerce suite
* SonataAdminBundle - The missing Symfony2 Admin Generator
* SonataMediaBundle
* SonataPageBundle
* SonataUserBundle
* SonataEasyExtendsBundle
* SonataIntlBundle
* SonataNewsBundle
* SonatajQueryBundle
* FOSUserBundle

Installation
------------

Run the following commands::

    curl -s http://getcomposer.org/installer | php
    php composer.phar create-project sonata-project/sandbox:dev-2.3-develop

Run
---

If you are running PHP5.4, you can use the built in server to start the demo::

    php -S localhost:9090 -t web

Now open your browser and go to http://localhost:9090/

Tests
-----

Functional testing
~~~~~~~~~~~~~~~~~~

To run the Behat tests, copy the default configuration file and adjust the base_url to your needs
::

    # behat.yml
    imports:
        - behat.yml.dist

    # Overwrite only the config you want to change here

You can now run the tests suite using the following command
::

    php bin/behat

To get more informations about Behat, feel free to check `the official documentation
<http://docs.behat.org/>`_.


Unit testing
~~~~~~~~~~~~

To run the Sonata test suites, you can run the command::

    bin/test_client_ci.sh

Enjoy!