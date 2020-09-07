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
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class TagController
{
    /**
     * @var TagManagerInterface
     */
    protected $tagManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(TagManagerInterface $tagManager, FormFactoryInterface $formFactory)
    {
        $this->tagManager = $tagManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Retrieves the list of tags (paginated) based on criteria.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the list of tags (paginated) based on criteria.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for tag list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of tags by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/Disabled tags filter",
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
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for tag list pagination")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of tags by page")
     * @Rest\QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled tags filter")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getTagsAction(ParamFetcherInterface $paramFetcher)
    {
        $page = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        /** @var PagerInterface $tagsPager */
        $tagsPager = $this->tagManager->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $tagsPager;
    }

    /**
     * Retrieves a specific tag.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific tag.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Tag"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when tag is not found"
     *     )
     * )
     *
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Tag identifier
     *
     * @return TagInterface
     */
    public function getTagAction($id)
    {
        return $this->getTag($id);
    }

    /**
     * Adds a tag.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a tag.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Tag"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while tag creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find tag"
     *     )
     * )
     *
     *
     *
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return TagInterface
     */
    public function postTagAction(Request $request)
    {
        return $this->handleWriteTag($request);
    }

    /**
     * Updates a tag.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a tag.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\ClassificationBundle\Model\Tag"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while tag update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find tag"
     *     )
     * )
     *
     *
     *
     * @param string  $id      Tag identifier
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return TagInterface
     */
    public function putTagAction($id, Request $request)
    {
        return $this->handleWriteTag($request, $id);
    }

    /**
     * Deletes a tag.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a tag.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when tag is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while tag deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find tag"
     *     )
     * )
     *
     *
     *
     * @param string $id Tag identifier
     *
     * @throws NotFoundHttpException
     *
     * @return Rest\View
     */
    public function deleteTagAction($id)
    {
        $tag = $this->getTag($id);

        $this->tagManager->delete($tag);

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
     * Retrieves tag with id $id or throws an exception if it doesn't exist.
     *
     * @param string $id Tag identifier
     *
     * @throws NotFoundHttpException
     *
     * @return TagInterface
     */
    protected function getTag($id)
    {
        $tag = $this->tagManager->find($id);

        if (null === $tag) {
            throw new NotFoundHttpException(sprintf('Tag (%d) not found', $id));
        }

        return $tag;
    }

    /**
     * Write a tag, this method is used by both POST and PUT action methods.
     *
     * @param Request     $request Symfony request
     * @param string|null $id      Tag identifier
     *
     * @return FormInterface
     */
    protected function handleWriteTag($request, $id = null)
    {
        $tag = $id ? $this->getTag($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_classification_api_form_tag', $tag, [
            'csrf_protection' => false,
        ]);

        FormHelper::removeFields($request->request->all(), $form);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tag = $form->getData();
            $this->tagManager->save($tag);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            $view = View::create($tag);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
