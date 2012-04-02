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
* SonatajQueryBundle

FOS Bundles
~~~~~~~~~~~

* FOSUserBundle

Behat Bundles
~~~~~~~~~~~~~

* MinkBundle
* BehatBundle

Installation
------------

Run the following commands::

    git clone http://github.com/sonata-project/sandbox.git sonata-sandbox
    cd sonata-sandbox
    rm -rf .git
    git init
    git add .gitignore *
    git commit -m "Initial commit (from the Sonata Sandbox)"
    php bin/vendors install
    git add *
    git commit -m "add submodules"
    php bin/build_bootstrap.php
    cp app/config/parameters.yml.sample app/config/parameters.yml
    cp app/config/parameters.yml.sample app/config/validation_parameters.yml
    cp app/config/parameters.yml.sample app/config/production_parameters.yml

.. note::

  The ``bin/vendor`` script does not behave like the one provided by the Symfony2 Standard Edition.
  The script install vendors as git submodules.

Database initialization
~~~~~~~~~~~~~~~~~~~~~~~

At this point, the ``app/console`` command should start with no issues. However some you need the complete some others step:

* database configuration (edit the app/config/parameters.yml file)

then runs the commands::

    app/console doctrine:database:create
    app/console doctrine:schema:create

Assets Installation
~~~~~~~~~~~~~~~~~~~
Your frontend still looking weird because bundle assets are not installed. Run the following command to install assets for all active bundles under public directory::

    app/console assets:install web


Sonata Page Bundle
~~~~~~~~~~~~~~~~~~

By default the Sonata Page bundle is activated, so you need to starts 2 commands before going further::

    app/console sonata:page:create-site --enabled=true --name=localhost --host=localhost --relativePath=/ --enabledFrom=now --enabledTo="+10 years" --default=true
    app/console sonata:page:update-core-routes --site=all
    app/console sonata:page:create-snapshots --site=all

.. note::

    The ``update-core-routes`` populates the database with ``page`` from the routing information.
    The ``create-snapshots`` create a snapshot (a public page version) from the created pages.


Fixtures
~~~~~~~~~~~~~~~~~~

To have some actual data in your DB, you should load the fixtures by running::

    app/console doctrine:fixtures:load


Unit Testing
------------

Automatic Unit Testing with ``watchr``::

    gem install watchr
    cd /path/to/symfony-project
    watchr phpunit.watchr


reference : https://gist.github.com/1151531

Enjoy!
