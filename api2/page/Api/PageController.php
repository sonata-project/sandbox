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

namespace Sonata\PageBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Model\BlockManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PageController extends FOSRestController
{
    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    /**
     * @var PageManagerInterface
     */
    protected $pageManager;

    /**
     * @var BlockManagerInterface
     */
    protected $blockManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var BackendInterface
     */
    protected $backend;

    public function __construct(SiteManagerInterface $siteManager, PageManagerInterface $pageManager, BlockManagerInterface $blockManager, FormFactoryInterface $formFactory, BackendInterface $backend)
    {
        $this->siteManager = $siteManager;
        $this->pageManager = $pageManager;
        $this->blockManager = $blockManager;
        $this->formFactory = $formFactory;
        $this->backend = $backend;
    }

    /**
     * Retrieves the list of pages (paginated).
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the list of pages (paginated).",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for 'page' list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of pages by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/Disabled pages filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="edited",
     *         in="query",
     *         description="Edited/Up to date pages filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="internal",
     *         in="query",
     *         description="Internal/Exposed pages filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="root",
     *         in="query",
     *         description="Filter pages having no parent id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="site",
     *         in="query",
     *         description="Filter pages for a specific site's id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="parent",
     *         in="query",
     *         description="Get pages being child of given page id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Order by array (key is field, value is direction)",
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
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for 'page' list pagination")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of pages by page")
     * @Rest\QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled pages filter")
     * @Rest\QueryParam(name="edited", requirements="0|1", nullable=true, strict=true, description="Edited/Up to date pages filter")
     * @Rest\QueryParam(name="internal", requirements="0|1", nullable=true, strict=true, description="Internal/Exposed pages filter")
     * @Rest\QueryParam(name="root", requirements="0|1", nullable=true, strict=true, description="Filter pages having no parent id")
     * @Rest\QueryParam(name="site", requirements="\d+", nullable=true, strict=true, description="Filter pages for a specific site's id")
     * @Rest\QueryParam(name="parent", requirements="\d+", nullable=true, strict=true, description="Get pages being child of given page id")
     * @Rest\QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Order by array (key is field, value is direction)")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getPagesAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'enabled' => '',
            'edited' => '',
            'internal' => '',
            'root' => '',
            'site' => '',
            'parent' => '',
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

        $pager = $this->pageManager->getPager($criteria, $page, $limit, $sort);

        return $pager;
    }

    /**
     * Retrieves a specific page.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific page.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\PageBundle\Model\PageInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when page is not found"
     *     )
     * )
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Page identifier
     *
     * @return PageInterface
     */
    public function getPageAction($id)
    {
        return $this->getPage($id);
    }

    /**
     * Retrieves a specific page's blocks.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific page's blocks.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\BlockBundle\Model\BlockInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when page is not found"
     *     )
     * )
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Page identifier
     *
     * @return BlockInterface[]
     */
    public function getPageBlocksAction($id)
    {
        return $this->getPage($id)->getBlocks();
    }

    /**
     * Retrieves a specific page's child pages.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific page's child pages.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\BlockBundle\Model\BlockInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when page is not found"
     *     )
     * )
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Page identifier
     *
     * @return PageInterface[]
     */
    public function getPagePagesAction($id)
    {
        $page = $this->getPage($id);

        return $page->getChildren();
    }

    /**
     * Adds a block.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a block.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\PageBundle\Model\Block"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while block creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find page"
     *     )
     * )
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string  $id      Page identifier
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return BlockInterface
     */
    public function postPageBlockAction($id, Request $request)
    {
        $page = $id ? $this->getPage($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_page_api_form_block', null, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $block = $form->getData();
            $block->setPage($page);

            $this->blockManager->save($block);

            return $block;
        }

        return $form;
    }

    /**
     * Adds a page.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a page.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\PageBundle\Model\Page"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while page creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find page"
     *     )
     * )
     *
     *
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return PageInterface
     */
    public function postPageAction(Request $request)
    {
        return $this->handleWritePage($request);
    }

    /**
     * Updates a page.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a page.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\PageBundle\Model\Page"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while page update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find page"
     *     )
     * )
     *
     *
     * @param string  $id      Page identifier
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return PageInterface
     */
    public function putPageAction($id, Request $request)
    {
        return $this->handleWritePage($request, $id);
    }

    /**
     * Deletes a page.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a page.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when page is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while page deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find page"
     *     )
     * )
     *
     *
     * @param string $id Page identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deletePageAction($id)
    {
        $page = $this->getPage($id);

        $this->pageManager->delete($page);

        return ['deleted' => true];
    }

    /**
     * Creates snapshots of a page.
     *
     * @Operation(
     *     tags={""},
     *     summary="Creates snapshots of a page.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when snapshots are successfully queued for creation"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while snapshots creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find page"
     *     )
     * )
     *
     *
     * @param string $id Page identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function postPageSnapshotAction($id)
    {
        $page = $this->getPage($id);

        $this->backend->createAndPublish('sonata.page.create_snapshot', [
            'pageId' => $page->getId(),
        ]);

        return ['queued' => true];
    }

    /**
     * Creates snapshots of all pages.
     *
     * @Operation(
     *     tags={""},
     *     summary="Creates snapshots of all pages.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when snapshots are successfully queued for creation"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while snapshots creation"
     *     )
     * )
     *
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function postPagesSnapshotsAction()
    {
        $sites = $this->siteManager->findAll();

        foreach ($sites as $site) {
            $this->backend->createAndPublish('sonata.page.create_snapshot', [
                'siteId' => $site->getId(),
            ]);
        }

        return ['queued' => true];
    }

    /**
     * Retrieves page with id $id or throws an exception if it doesn't exist.
     *
     * @param string $id Page identifier
     *
     * @throws NotFoundHttpException
     *
     * @return PageInterface
     */
    protected function getPage($id)
    {
        $page = $this->pageManager->findOneBy(['id' => $id]);

        if (null === $page) {
            throw new NotFoundHttpException(sprintf('Page (%d) not found', $id));
        }

        return $page;
    }

    /**
     * Retrieves Block with id $id or throws an exception if it doesn't exist.
     *
     * @param string $id Block identifier
     *
     * @throws NotFoundHttpException
     *
     * @return BlockInterface
     */
    protected function getBlock($id)
    {
        $block = $this->blockManager->findOneBy(['id' => $id]);

        if (null === $block) {
            throw new NotFoundHttpException(sprintf('Block (%d) not found', $id));
        }

        return $block;
    }

    /**
     * Write a page, this method is used by both POST and PUT action methods.
     *
     * @param Request     $request Symfony request
     * @param string|null $id      Page identifier
     *
     * @return FormInterface
     */
    protected function handleWritePage($request, $id = null)
    {
        $page = $id ? $this->getPage($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_page_api_form_page', $page, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $page = $form->getData();
            $this->pageManager->save($page);

            return $this->serializeContext($page, ['sonata_api_read']);
        }

        return $form;
    }
}
