<?php

namespace Application\Sonata\ProductBundle\Controller;

use Sonata\ProductBundle\Controller\BaseProductController as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseProductController extends BaseController
{
    /**
     * Controller override to allow usage of SonataSeoBundle features
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function viewAction($product)
    {
        $view = parent::viewAction($product);

        $this->container->get('sonata.seo.page')
            // Twitter metadata
            ->addMeta('name', 'twitter:card', 'product')
            ->addMeta('name', 'twitter:title', $product->getName())
            ->addMeta('name', 'twitter:description', $product->getDescription())
            ->addMeta('name', 'twitter:label1', 'Price')
            ->addMeta('name', 'twitter:data1', sprintf('%.2f%s', $product->getPrice(), $this->get('sonata.price.currency.detector')->getCurrency()));

        return $view;
    }
}