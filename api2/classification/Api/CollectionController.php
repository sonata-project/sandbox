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
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class CollectionController
{
    /**
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(CollectionManagerInterface $collectionManager, FormFactoryInterface $formFactory)
    {
        $this->collectionManager = $collectionManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Retrieves the list of collections (paginated) based on criteria.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the list of collections (paginated) based on criteria.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for collection list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of collections by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/Disabled collections filter",
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
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for collection list pagination")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of collections by page")
     * @Rest\QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled collections filter")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getCollectionsAction(ParamFetcherInterface $paramFetcher)
    {
        $page = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        /** @var PagerInterface $collectionsPager */
        $collectionsPager = $this->collectionManager->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $collectionsPager;
    }

    /**
     * Retrieves a specific collection.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific collection.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Collection"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when collection is not found"
     *     )
     * )
     *
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Collection identifier
     *
     * @return CollectionInterface
     */
    public function getCollectionAction($id)
    {
        return $this->getCollection($id);
    }

    /**
     * Adds a collection.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a collection.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Collection"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while collection creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find collection"
     *     )
     * )
     *
     *
     *
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return CollectionInterface
     */
    public function postCollectionAction(Request $request)
    {
        return $this->handleWriteCollection($request);
    }

    /**
     * Updates a collection.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a collection.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Collection"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while collection update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find collection"
     *     )
     * )
     *
     *
     *
     * @param string  $id      Collection identifier
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return CollectionInterface
     */
    public function putCollectionAction($id, Request $request)
    {
        return $this->handleWriteCollection($request, $id);
    }

    /**
     * Deletes a collection.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a collection.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when collection is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while collection deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find collection"
     *     )
     * )
     *
     *
     *
     * @param string $id Collection identifier
     *
     * @throws NotFoundHttpException
     *
     * @return Rest\View
     */
    public function deleteCollectionAction($id)
    {
        $collection = $this->getCollection($id);

        $this->collectionManager->delete($collection);

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
     * Retrieves collection with id $id or throws an exception if it doesn't exist.
     *
     * @param string $id Collection identifier
     *
     * @throws NotFoundHttpException
     *
     * @return CollectionInterface
     */
    protected function getCollection($id)
    {
        $collection = $this->collectionManager->find($id);

        if (null === $collection) {
            throw new NotFoundHttpException(sprintf('Collection (%d) not found', $id));
        }

        return $collection;
    }

    /**
     * Write a collection, this method is used by both POST and PUT action methods.
     *
     * @param Request     $request Symfony request
     * @param string|null $id      Collection identifier
     *
     * @return Rest\View|FormInterface
     */
    protected function handleWriteCollection($request, $id = null)
    {
        $collection = $id ? $this->getCollection($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_classification_api_form_collection', $collection, [
            'csrf_protection' => false,
        ]);

        FormHelper::removeFields($request->request->all(), $form);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $collection = $form->getData();
            $this->collectionManager->save($collection);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            $view = View::create($collection);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
