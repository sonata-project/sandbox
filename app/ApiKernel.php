<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

include_once __DIR__ . '/BaseKernel.php';

class ApiKernel extends BaseKernel
{
    public function registerBundles()
    {
        return array_merge(parent::registerBundles(), array(
            // API
            new FOS\RestBundle\FOSRestBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
        ));
    }
}
