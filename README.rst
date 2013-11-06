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
    php -S localhost:9090 -t web

.. note::

    The load_data.sh script which is ran automatically upon create-project runs also these commands:
    The ```sonata:admin:setup-acl``` and ```sonata:admin:generate-object-acl``` set ACL security handler.
    The ``sonata:page:update-core-routes`` populates the database with ``page`` from the routing information.
    The ``sonata:page:create-snapshots`` create a snapshot (a public page version) from the created pages.

Tests
-----

Functional testing
~~~~~~~~~~~~~~~~~~

To run the Behat tests, create a configuration file importing the dist version:
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
