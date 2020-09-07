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

namespace Sonata\ClassificationBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\ClassificationBundle\Form\FormHelper;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class CategoryController
{
    /**
     * @var CategoryManagerInterface
     */
    protected $categoryManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(CategoryManagerInterface $categoryManager, FormFactoryInterface $formFactory)
    {
        $this->categoryManager = $categoryManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Retrieves the list of categories (paginated) based on criteria.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the list of categories (paginated) based on criteria.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for category list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of categories by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/Disabled categories filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="context",
     *         in="query",
     *         description="Context of categories",
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
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for category list pagination")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of categories by page")
     * @Rest\QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled categories filter")
     * @Rest\QueryParam(name="context", requirements="\S+", nullable=true, strict=true, description="Context of categories")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getCategoriesAction(ParamFetcherInterface $paramFetcher)
    {
        $page = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        /** @var PagerInterface $categoriesPager */
        $categoriesPager = $this->categoryManager->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $categoriesPager;
    }

    /**
     * Retrieves a specific category.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific category.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Category"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when category is not found"
     *     )
     * )
     *
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Category identifier
     *
     * @return CategoryInterface
     */
    public function getCategoryAction($id)
    {
        return $this->getCategory($id);
    }

    /**
     * Adds a category.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a category.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Category"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while category creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find category"
     *     )
     * )
     *
     *
     *
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return CategoryInterface
     */
    public function postCategoryAction(Request $request)
    {
        return $this->handleWriteCategory($request);
    }

    /**
     * Updates a category.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a category.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Category"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while category update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find category"
     *     )
     * )
     *
     *
     *
     * @param string  $id      Category identifier
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return CategoryInterface
     */
    public function putCategoryAction($id, Request $request)
    {
        return $this->handleWriteCategory($request, $id);
    }

    /**
     * Deletes a category.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a category.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when category is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while category deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find category"
     *     )
     * )
     *
     *
     *
     * @param string $id Category identifier
     *
     * @throws NotFoundHttpException
     *
     * @return Rest\View
     */
    public function deleteCategoryAction($id)
    {
        $category = $this->getCategory($id);

        $this->categoryManager->delete($category);

        return ['deleted' => true];
    }

    /**
     * Filters criteria from $paramFetcher to be compatible with the Pager criteria.
     *
     * @return array The filtered criteria
     */
    protected function filterCriteria(ParamFetcherInterface $paramFetcher)
    {
        $criteria = $paramFetcher->all();

        unset($criteria['page'], $criteria['count']);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        return $criteria;
    }

    /**
     * Retrieves category with id $id or throws an exception if it doesn't exist.
     *
     * @param string $id Category identifier
     *
     * @throws NotFoundHttpException
     *
     * @return CategoryInterface
     */
    protected function getCategory($id)
    {
        $category = $this->categoryManager->find($id);

        if (null === $category) {
            throw new NotFoundHttpException(sprintf('Category (%d) not found', $id));
        }

        return $category;
    }

    /**
     * Write a category, this method is used by both POST and PUT action methods.
     *
     * @param Request     $request Symfony request
     * @param string|null $id      category identifier
     *
     * @return Rest\View|FormInterface
     */
    protected function handleWriteCategory($request, $id = null)
    {
        $category = $id ? $this->getCategory($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_classification_api_form_category', $category, [
            'csrf_protection' => false,
        ]);

        FormHelper::removeFields($request->request->all(), $form);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $category = $form->getData();
            $this->categoryManager->save($category);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            $view = View::create($category);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
