Sonata Standard Edition
=======================

What's inside?
--------------

Sonata Standard Edition comes pre-configured with the following bundles:

Symfony Standard Edition
~~~~~~~~~~~~~~~~~~~~~~~~

* FrameworkBundle
* SensioFrameworkExtraBundle
* DoctrineBundle
* TwigBundle
* SwiftmailerBundle
* MonologBundle
* AsseticBundle
* JMSSecurityExtraBundle
* WebProfilerBundle (in dev/test env)
* SensioDistributionBundle (in dev/test env)
* SensioGeneratorBundle (in dev/test env)
* AcmeDemoBundle (in dev/test env)

Sonata Bundles
~~~~~~~~~~~~~~

* SonataAdminBundle - The missing Symfony2 Admin Generator
* SonataMediaBundle
* SonataPageBundle
* SonataUserBundle
* SonataEasyExtendsBundle
* SonataIntlBundle
* SonataNewsBundle
* SonataBluePrintBundle
* SonatajQueryBundle

FOS Bundles
~~~~~~~~~~~

* FOSUserBundle

Behat Bundles
~~~~~~~~~~~

* MinkBundle
* BehatBundle

Installation from Git
---------------------

Run the following commands:

    git clone http://github.com/sonata-project/sandbox.git sonata-sandbox
    cd sonata-sandbox
    rm -rf .git
    php bin/vendors install

.. note::

    Sonata Distributio does/can not use git submodules as you should not keep the
    `.git` directory.

Configuration
-------------

Check that everything is working fine by going to the ``web/config.php`` page
in a browser and follow the instructions.

The distribution is configured with the following defaults:

* Twig is the only configured template engine;
* Doctrine ORM/DBAL is configured;
* Swiftmailer is configured;
* Annotations for everything are enabled.

A default bundle, ``AcmeDemoBundle``, shows you Symfony2 in action. After
playing with it, you can remove it by following these steps:

* delete the ``src/Acme`` directory;
* remove the routing entries referencing AcmeBundle in ``app/config/routing_dev.yml``;
* remove the AcmeBundle from the registered bundles in ``app/AppKernel.php``;

Configure the distribution by editing ``app/config/parameters.ini`` or by
accessing ``web/config.php`` in a browser.

A simple controller is configured at ``/hello/{name}``. Access it via
``web/app_dev.php/demo/hello/Fabien``.

If you want to use the CLI, a console application is available at
``app/console``. Check first that your PHP is correctly configured for the CLI
by running ``app/check.php``.

Enjoy!
