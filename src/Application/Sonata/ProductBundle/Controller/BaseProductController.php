<?php

namespace Application\Sonata\ProductBundle\Controller;

use Sonata\ProductBundle\Controller\BaseProductController as BaseController;

abstract class BaseProductController extends BaseController
{
    /**
     * Controller override to allow usage of SonataSeoBundle features
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($product)
    {
        $view = parent::viewAction($product);
        $seoPage = $this->container->get('sonata.seo.page');
        $currency = $this->get('sonata.price.currency.detector')->getCurrency();

        $seoPage
            // Twitter metadata
            ->addMeta('name', 'twitter:card', 'product')
            ->addMeta('name', 'twitter:title', $product->getName())
            ->addMeta('name', 'twitter:description', $product->getDescription())
            ->addMeta('name', 'twitter:label1', 'Price')
            ->addMeta('name', 'twitter:data1', sprintf('%.2f%s', $product->getPrice(), $currency))

            // Facebook OpenGraph metadata
            ->addMeta('property', 'og:type', 'og:product')
            ->addMeta('property', 'og:title', $product->getName())
            ->addMeta('property', 'og:description', $product->getDescription())
            ->addMeta('property', 'product:price:amount', $product->getPrice())
            ->addMeta('property', 'product:price:currency', $currency)
            ->addMeta('property', 'og:url', $this->getRequest()->getUri())
            ;

        // If a media is available, we add the opengraph image data
        if ($image = $product->getImage()) {
            $format = 'reference';
            $provider = $this->get('sonata.media.pool')
                ->getProvider($image->getProviderName());

            $seoPage->addMeta('property', 'og:image:type', $provider->generatePublicUrl($image, $format))
                ->addMeta('property', 'og:image:width', $image->getWidth())
                ->addMeta('property', 'og:image:height', $image->getHeight());
        }


        return $view;
    }
}