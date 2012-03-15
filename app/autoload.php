<?php

if (!class_exists('Symfony\Component\ClassLoader\UniversalClassLoader', false)) {
    require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
}

if (!class_exists('Symfony\Component\ClassLoader\ApcUniversalClassLoader', false)) {
    require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/ApcUniversalClassLoader.php';
}

use Symfony\Component\ClassLoader\ApcUniversalClassLoader;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

//$loader = new \Symfony\Component\ClassLoader\ApcUniversalClassLoader('project_'.filemtime(__DIR__));
$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'Symfony'           => array(
        __DIR__.'/../vendor/symfony/src',
        __DIR__.'/../vendor/bundles'
    ),
    'Sensio'            => __DIR__.'/../vendor/bundles',
    'JMS'               => __DIR__.'/../vendor/bundles',
    'Doctrine\\Bundle'                => __DIR__.'/../vendor/bundles',
    'Doctrine\\DBAL\\Migrations'      => __DIR__.'/../vendor/doctrine-migrations/lib',
    'Doctrine\\Common\\DataFixtures'  => __DIR__.'/../vendor/doctrine-data-fixtures/lib',
    'Doctrine\\Common'                => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL'                  => __DIR__.'/../vendor/doctrine-dbal/lib',
    'Doctrine\\ORM'                   => __DIR__.'/../vendor/doctrine/lib',
    'Monolog'           => __DIR__.'/../vendor/monolog/src',
    'Assetic'           => __DIR__.'/../vendor/assetic/src',
    'Metadata'          => __DIR__.'/../vendor/metadata/src',
    'FOS'               => __DIR__.'/../vendor/bundles',
    'Knp'               => array(
        __DIR__.'/../vendor/bundles',
        __DIR__.'/../vendor/knp/menu/src'
    ),
    'Sonata'            => array(
        __DIR__.'/../vendor/sonata/src',
        __DIR__.'/../vendor/bundles',
        __DIR__.'/../src',
        __DIR__.'/../vendor/sonata-doctrine-extensions/src'
    ),
    'Imagine'           => __DIR__.'/../vendor/imagine/lib',
    'Gaufrette'         => __DIR__.'/../vendor/gaufrette/src',
    'Buzz'              => __DIR__.'/../vendor/buzz/lib',
    'Exporter'          => __DIR__.'/../vendor/exporter/lib',
    'Application'       => __DIR__.'/../src',
    'Behat\Mink'        => __DIR__.'/../vendor/behat/mink/src',
    'Behat\MinkBundle'  => __DIR__.'/../vendor/bundles',
    'Behat\SahiClient'  => __DIR__.'/../vendor/behat/sahi/src',
    'Behat\BehatBundle' => __DIR__.'/../vendor/bundles',
    'Behat\Behat'       => __DIR__.'/../vendor/behat/behat/src',
    'Behat\Gherkin'     => __DIR__.'/../vendor/behat/gherkin/src',
    'Bazinga'           => __DIR__.'/../vendor/bundles',
    'Faker'             => __DIR__.'/../vendor/faker/src',
    'SimpleThings'      => __DIR__.'/../vendor/bundles',
));

$loader->registerPrefixes(array(
    'Twig_Extensions_' => __DIR__.'/../vendor/twig-extensions/lib',
    'Twig_'            => __DIR__.'/../vendor/twig/lib',
));
$loader->registerPrefixFallbacks(array(
    __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs',
));
$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));
$loader->register();

AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

// Swiftmailer needs a special autoloader to allow
// the lazy loading of the init file (which is expensive)
require_once __DIR__.'/../vendor/swiftmailer/lib/classes/Swift.php';
Swift::registerAutoload(__DIR__.'/../vendor/swiftmailer/lib/swift_init.php');
