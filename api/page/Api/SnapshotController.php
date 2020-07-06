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
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\PageBundle\Model\SnapshotInterface;
use Sonata\PageBundle\Model\SnapshotManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
 */
class SnapshotController extends FOSRestController
{
    /**
     * @var SnapshotManagerInterface
     */
    protected $snapshotManager;

    public function __construct(SnapshotManagerInterface $snapshotManager)
    {
        $this->snapshotManager = $snapshotManager;
    }

    /**
     * Retrieves the list of snapshots (paginated).
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the list of snapshots (paginated).",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for snapshots list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Maximum number of snapshots per page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="site",
     *         in="query",
     *         description="Filter snapshots for a specific site's id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="page_id",
     *         in="query",
     *         description="Filter snapshots for a specific page's id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="root",
     *         in="query",
     *         description="Filter snapshots having no parent id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="parent",
     *         in="query",
     *         description="Get snapshots being child of given snapshots id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/Disabled snapshots filter",
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
     * @Rest\Get("/snapshots.{_format}", name="get_snapshots")
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for snapshots list pagination")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Maximum number of snapshots per page")
     * @QueryParam(name="site", requirements="\d+", nullable=true, strict=true, description="Filter snapshots for a specific site's id")
     * @QueryParam(name="page_id", requirements="\d+", nullable=true, strict=true, description="Filter snapshots for a specific page's id")
     * @QueryParam(name="root", requirements="0|1", nullable=true, strict=true, description="Filter snapshots having no parent id")
     * @QueryParam(name="parent", requirements="\d+", nullable=true, strict=true, description="Get snapshots being child of given snapshots id")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled snapshots filter")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Order by array (key is field, value is direction)")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getSnapshotsAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'enabled' => '',
            'site' => '',
            'page_id' => '',
            'root' => '',
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

        $pager = $this->snapshotManager->getPager($criteria, $page, $limit, $sort);

        return $pager;
    }

    /**
     * Retrieves a specific snapshot.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific snapshot.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\PageBundle\Model\SnapshotInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when snapshots is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/snapshots/{id}.{_format}", name="get_snapshot")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return SnapshotInterface
     */
    public function getSnapshotAction($id)
    {
        return $this->getSnapshot($id);
    }

    /**
     * Deletes a snapshot.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a snapshot.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when snapshots is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while deleting the snapshots"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find the snapshots"
     *     )
     * )
     *
     *
     * @Rest\Delete("/snapshots/{id}.{_format}", name="delete_snapshot")
     *
     * @param int $id A Snapshot identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteSnapshotAction($id)
    {
        $snapshots = $this->getSnapshot($id);

        $this->snapshotManager->delete($snapshots);

        return ['deleted' => true];
    }

    /**
     * Retrieves Snapshot with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     *
     * @return SnapshotInterface
     */
    protected function getSnapshot($id)
    {
        $snapshot = $this->snapshotManager->findOneBy(['id' => $id]);

        if (null === $snapshot) {
            throw new NotFoundHttpException(sprintf('Snapshot (%d) not found', $id));
        }

        return $snapshot;
    }
}
