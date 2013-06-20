<?php

namespace Sonata\Bundle\EcommerceDemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sonata\ProductBundle\Controller\BaseProductController;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Order\OrderElementInterface;

/**
 *
 * overwrite methods from the BaseProductController if you want to change the behavior
 * for the current product
 *
 */
class SoftwareController extends BaseProductController
{
    /**
     * {@inheritDoc}
     */
    public function viewVariationsAction($productId, $slug)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function viewBasketElement(BasketElementInterface $basketElement)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function viewBasketElementConfirmation(BasketElementInterface $basketElement)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function viewOrderElement(OrderElementInterface $orderElement)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function viewEditOrderElement(OrderElementInterface $orderElement)
    {

    }
}