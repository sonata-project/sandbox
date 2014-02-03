<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;


class AdminKernel extends BaseKernel
{
    public function registerBundles()
    {
        return array_merge(parent::registerBundles(), array(
            // insert your front bundles here
            new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),

            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),

            // @todo: remove this bundle
            new Application\Sonata\AdminBundle\ApplicationSonataAdminBundle(),

            // Disable this if you don't want the audit on entities
            new SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),
        ));
    }
}
