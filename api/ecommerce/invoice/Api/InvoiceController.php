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

namespace Sonata\InvoiceBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Invoice\InvoiceManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
 */
class InvoiceController
{
    /**
     * @var \Sonata\Component\Invoice\InvoiceManagerInterface
     */
    protected $invoiceManager;

    public function __construct(InvoiceManagerInterface $invoiceManager)
    {
        $this->invoiceManager = $invoiceManager;
    }

    /**
     * Returns a paginated list of invoices.
     *
     * @Operation(
     *     tags={""},
     *     summary="Returns a paginated list of invoices.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for invoices list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of invoices by page",
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
     *         name="status",
     *         in="query",
     *         description="Filter on invoice statuses",
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
     * @Rest\Get("/invoices.{_format}", name="get_invoices")
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for invoices list pagination")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of invoices by page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Sort specification for the resultset (key is field, value is direction")
     * @QueryParam(name="status", requirements="\d+", nullable=true, strict=true, description="Filter on invoice statuses")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return \Sonata\DatagridBundle\Pager\PagerInterface
     */
    public function getInvoicesAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'status' => '',
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

        return $this->invoiceManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific invoice.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific invoice.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Invoice\InvoiceInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when invoice is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/invoices/{id}.{_format}", name="get_invoice")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return InvoiceInterface
     */
    public function getInvoiceAction($id)
    {
        return $this->getInvoice($id);
    }

    /**
     * Retrieves a specific invoice's elements.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific invoice's elements.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Invoice\InvoiceElementInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when invoice is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/invoices/{id}/invoiceelements.{_format}", name="get_invoice_invoiceelements")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return array
     */
    public function getInvoiceInvoiceelementsAction($id)
    {
        return $this->getInvoice($id)->getInvoiceElements();
    }

    /**
     * Retrieves invoice with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return InvoiceInterface
     */
    protected function getInvoice($id)
    {
        $invoice = $this->invoiceManager->findOneBy(['id' => $id]);

        if (null === $invoice) {
            throw new NotFoundHttpException(sprintf('Invoice (%d) not found', $id));
        }

        return $invoice;
    }
}
