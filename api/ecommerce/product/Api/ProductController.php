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
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View as FOSRestView;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductCollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
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
     * @Operation(
     *     tags={""},
     *     summary="Returns a paginated list of products.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for products list pagination (1-indexed)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of products by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Sort specification for the resultset (key is field, value is direction",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/disabled products only?",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\DatagridBundle\Pager\PagerInterface"))
     *     )
     * )
     *
     *
     * @Rest\Get("/products.{_format}", name="get_products")
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for products list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of products by page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Sort specification for the resultset (key is field, value is direction")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/disabled products only?")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return \Sonata\DatagridBundle\Pager\PagerInterface
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
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Product\ProductInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}.{_format}", name="get_product")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @Post("/{provider}/products")
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a product depending on the product provider.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ProductBundle\Entity\BaseProduct"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while product creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find product"
     *     )
     * )
     *
     *
     *
     * @Post("/products/{provider}.{_format}", name="post_product", requirements={"provider"="[A-Za-z0-9._]"})
     *
     * @param string  $provider A product provider name
     * @param Request $request  A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function postProductAction($provider, Request $request)
    {
        return $this->handleWriteProduct($provider, $request);
    }

    /**
     * Updates a product.
     *
     * @Put("/{provider}/products/{id}")
     * @Operation(
     *     tags={""},
     *     summary="Updates a product.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ProductBundle\Entity\BaseProduct"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while product update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find product"
     *     )
     * )
     *
     *
     *
     * @Put("/products/{id}.{_format}", name="put_product", requirements={"provider"="[A-Za-z0-9.]*"})
     *
     * @param int     $id       A Product identifier
     * @param string  $provider A product provider name
     * @param Request $request  A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function putProductAction($id, $provider, Request $request)
    {
        return $this->handleWriteProduct($provider, $request, $id);
    }

    /**
     * Deletes a product.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a product.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when post is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while product deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find product"
     *     )
     * )
     *
     *
     * @Rest\Delete("/products/{id}.{_format}", name="delete_product")
     *
     * @param int $id A Product identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteProductAction($id)
    {
        $product = $this->getProduct($id);
        $manager = $this->productPool->getManager($product);

        try {
            $manager->delete($product);
        } catch (\Exception $e) {
            return FOSRestView::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Retrieves a specific product's ProductCategories.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product's ProductCategories.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Product\ProductCategoryInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}/productcategories.{_format}", name="get_product_productcategories")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product's ProductCategories' categories.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\CategoryInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}/categories.{_format}", name="get_product_categories")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product's ProductCollections.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Product\ProductCollectionInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}/productcollections.{_format}", name="get_product_productcollections")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product's ProductCollections' collections.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\CollectionInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}/collections.{_format}", name="get_product_collections")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product's deliveries.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Product\DeliveryInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}/deliveries.{_format}", name="get_product_deliveries")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product's packages.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Product\PackageInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}/packages.{_format}", name="get_product_packages")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific product's variations.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Product\ProductInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when product is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/products/{id}/variations.{_format}", name="get_product_variations")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
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
     * @param string   $provider A product provider name
     * @param Request  $request  Symfony request
     * @param int|null $id       A product identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
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

            $view = FOSRestView::create($product);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }

    /**
     * Retrieves product with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
