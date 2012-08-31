<?php

namespace Sonata\Bundle\EcommerceDemoBundle\Provider;

use Sonata\ProductBundle\Model\BaseProductProvider;

class HardwareProvider extends BaseProductProvider
{
    /**
     * {@inheritDoc}
     */
    function getBaseControllerName()
    {
        return 'SonataEcommerceDemoBundle:Hardware';
    }
}