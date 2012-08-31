<?php

namespace Sonata\Bundle\EcommerceDemoBundle\Provider;

use Sonata\ProductBundle\Model\BaseProductProvider;

class SoftwareProvider extends BaseProductProvider
{
    /**
     * {@inheritDoc}
     */
    function getBaseControllerName()
    {
        return 'SonataEcommerceDemoBundle:Software';
    }
}