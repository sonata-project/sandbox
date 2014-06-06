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

### Front, Admin and API Controllers

The sandbox handles multiple kernels:

* ``AdminKernel`` for ``/admin/*`` urls
* ``ApiKernel`` for ``/api/*`` urls
* ``FrontKernel`` for all others urls

The current implementation tries to follow the default folder structure of a standard symfony distribution. However, there are a few minor things to know:

* ``cache`` folders are named as ``APPNAME_ENVNAME`` and located in ``PROJECT_FOLDER/cache``.
* ``logs`` files are named as ``APPNAME_ENVNAME.log`` and located in ``PROJECT_LOG/logs``.
* the configuration files are located in ``apps/KERNEL_NAME``.
* new bundles can be added to the BaseKernel if they are required on all subkernels.
* the ``./apps/console`` command behaves as the original one but with the ``AdminKernel`` as all bundles are almost available with this kernel.
* the ``./sonata`` command works as the ``app/console`` however the first argument must be the app name.
* a new ``kernel.conf_dir`` parameter is now available

The separation has been done to improve performance on the routing side and to avoid loading too much services on the main front controller.

### Sessions

Sessions are stored in the ``PROJECT_FOLDER/cache/sessions`` folder. Sessions are shared accross the AdminKernel and the FrontKernel, the ``security.yml`` also share the same settings with the ``context`` options.


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

Reset the data
--------------

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
