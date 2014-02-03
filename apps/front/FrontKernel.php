<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class FrontKernel extends BaseKernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array_merge(parent::registerBundles(), array(
            // insert your front bundles here
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            //new Symfony\Bundle\AsseticBundle\AsseticBundle(),
        ));
    }
}
