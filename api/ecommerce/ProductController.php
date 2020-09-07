<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductCollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductController
{
    /**
     * @var ProductManagerInterface
     */
    protected $productManager;

    /**
     * @var Pool
     */
    protected $productPool;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FormatterPool
     */
    protected $formatterPool;

    /**
     * @param ProductManagerInterface $productManager Sonata product manager
     * @param Pool                    $productPool    Sonata product pool
     * @param FormFactoryInterface    $formFactory    Symfony form factory
     */
    public function __construct(ProductManagerInterface $productManager, Pool $productPool, FormFactoryInterface $formFactory, FormatterPool $formatterPool)
    {
        $this->productManager = $productManager;
        $this->productPool = $productPool;
        $this->formFactory = $formFactory;
        $this->formatterPool = $formatterPool;
    }

    /**
     * Returns a paginated list of products.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for products list pagination (1-indexed)")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of products by page")
     * @Rest\QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Sort specification for the resultset (key is field, value is direction")
     * @Rest\QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/disabled products only?")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getProductsAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'enabled' => '',
        ];

        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('count');
        $sort = $paramFetcher->get('orderBy');
        $criteria = array_intersect_key($paramFetcher->all(), $supportedCriteria);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (!$sort) {
            $sort = [];
        } elseif (!\is_array($sort)) {
            $sort = [$sort => 'asc'];
        }

        return $this->productManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific product.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return ProductInterface
     */
    public function getProductAction($id)
    {
        return $this->getProduct($id);
    }

    /**
     * Adds a product depending on the product provider.
     *
     * @ApiDoc(
     *  resource=true,
     *  input={"class"="sonata_product_api_form_product", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ProductBundle\Entity\BaseProduct", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while product creation",
     *      404="Returned when unable to find product"
     *  }
     * )
     *
     * @param string  $provider Product provider name
     * @param Request $request  Symfony request
     *
     * @return View|FormInterface
     */
    public function postProductAction($provider, Request $request)
    {
        return $this->handleWriteProduct($provider, $request);
    }

    /**
     * Updates a product.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"},
     *      {"name"="provider", "dataType"="string", "requirement"="[A-Za-z0-9.]*", "description"="Product provider"}
     *  },
     *  input={"class"="sonata_product_api_form_product", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ProductBundle\Entity\BaseProduct", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while product update",
     *      404="Returned when unable to find product"
     *  }
     * )
     *
     * @param string  $id       Product identifier
     * @param string  $provider Product provider name
     * @param Request $request  Symfony request
     *
     * @return View|FormInterface
     */
    public function putProductAction($id, $provider, Request $request)
    {
        return $this->handleWriteProduct($provider, $request, $id);
    }

    /**
     * Deletes a product.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when post is successfully deleted",
     *      400="Returned when an error has occurred while product deletion",
     *      404="Returned when unable to find product"
     *  }
     * )
     *
     * @param string $id Product identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deleteProductAction($id)
    {
        $product = $this->getProduct($id);
        $manager = $this->productPool->getManager($product);

        try {
            $manager->delete($product);
        } catch (\Exception $e) {
            return View::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Retrieves a specific product's ProductCategories.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductCategoryInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return ProductCategoryInterface[]
     */
    public function getProductProductcategoriesAction($id)
    {
        return $this->getProduct($id)->getProductCategories();
    }

    /**
     * Retrieves a specific product's ProductCategories' categories.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\ClassificationBundle\Model\CategoryInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return CategoryInterface[]
     */
    public function getProductCategoriesAction($id)
    {
        return $this->getProduct($id)->getCategories();
    }

    /**
     * Retrieves a specific product's ProductCollections.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductCollectionInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return ProductCollectionInterface[]
     */
    public function getProductProductcollectionsAction($id)
    {
        return $this->getProduct($id)->getProductCollections();
    }

    /**
     * Retrieves a specific product's ProductCollections' collections.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\ClassificationBundle\Model\CollectionInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return CollectionInterface[]
     */
    public function getProductCollectionsAction($id)
    {
        return $this->getProduct($id)->getCollections();
    }

    /**
     * Retrieves a specific product's deliveries.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\Component\Product\DeliveryInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return DeliveryInterface[]
     */
    public function getProductDeliveriesAction($id)
    {
        return $this->getProduct($id)->getDeliveries();
    }

    /**
     * Retrieves a specific product's packages.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\Component\Product\PackageInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return PackageInterface[]
     */
    public function getProductPackagesAction($id)
    {
        return $this->getProduct($id)->getPackages();
    }

    /**
     * Retrieves a specific product's variations.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Product identifier"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Product identifier
     *
     * @return ProductInterface[]
     */
    public function getProductVariationsAction($id)
    {
        return $this->getProduct($id)->getVariations();
    }

    /**
     * Write a product, this method is used by both POST and PUT action methods.
     *
     * @param string      $provider Product provider name
     * @param Request     $request  Symfony request
     * @param string|null $id       Product identifier
     *
     * @return View|FormInterface
     */
    protected function handleWriteProduct($provider, $request, $id = null)
    {
        $product = $id ? $this->getProduct($id) : null;

        try {
            $manager = $this->productPool->getManager($provider);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        $form = $this->formFactory->createNamed(null, 'sonata_product_api_form_product', $product, [
            'csrf_protection' => false,
            'data_class' => $manager->getClass(),
            'provider_name' => $provider,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $product->setDescription($this->formatterPool->transform($product->getDescriptionFormatter(), $product->getRawDescription()));
            $product->setShortDescription($this->formatterPool->transform($product->getShortDescriptionFormatter(), $product->getRawShortDescription()));
            $manager->save($product);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            // simplify when dropping FOSRest < 2.1
            if (method_exists($context, 'enableMaxDepth')) {
                $context->enableMaxDepth();
            } else {
                $context->setMaxDepth(10);
            }

            $view = View::create($product);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }

    /**
     * Retrieves product with identifier $id or throws an exception if it doesn't exist.
     *
     * @param string $id Product identifier
     *
     * @throws NotFoundHttpException
     *
     * @return ProductInterface
     */
    protected function getProduct($id)
    {
        $product = $this->productManager->findOneBy(['id' => $id]);

        if (null === $product) {
            throw new NotFoundHttpException(sprintf('Product (%d) not found', $id));
        }

        return $product;
    }
}
