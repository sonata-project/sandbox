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

    git clone http://github.com/sonata-project/sandbox.git sonata-sandbox
    cd sonata-sandbox
    git checkout hackingday
    curl -s http://getcomposer.org/installer | php
    php composer.phar install
    php load_data.php
    cd web
    php -S localhost:9090

.. note::

    The setup.sh script runs also these commands.
    The ``update-core-routes`` populates the database with ``page`` from the routing information.
    The ``create-snapshots`` create a snapshot (a public page version) from the created pages.

Tests
-----

Functional testing
~~~~~~~~~~~~~~~~~~

To run the Behat tests, copy the default configuration file and adjust the base_url to your needs
::

    cp ./behat.yml.dist ./behat.yml

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
