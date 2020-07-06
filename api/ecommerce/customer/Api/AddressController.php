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

namespace Sonata\CustomerBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View as FOSRestView;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
 */
class AddressController
{
    /**
     * @var AddressManagerInterface
     */
    protected $addressManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(AddressManagerInterface $addressManager, FormFactoryInterface $formFactory)
    {
        $this->addressManager = $addressManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a paginated list of addresses.
     *
     * @Operation(
     *     tags={""},
     *     summary="Returns a paginated list of addresses.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for addresses list pagination (1-indexed)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of addresses by page",
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
     *         name="customer",
     *         in="query",
     *         description="Filter on customer id",
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
     * @Rest\Get("/addresses.{_format}", name="get_addresses")
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for addresses list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of addresses by page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Sort specification for the resultset (key is field, value is direction")
     * @QueryParam(name="customer", requirements="\d+", nullable=true, strict=true, description="Filter on customer id")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return \Sonata\DatagridBundle\Pager\PagerInterface
     */
    public function getAddressesAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'customer' => '',
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

        return $this->addressManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific address.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific address.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Customer\AddressInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when address is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/addresses/{id}.{_format}", name="get_address")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return AddressInterface
     */
    public function getAddressAction($id)
    {
        return $this->getAddress($id);
    }

    /**
     * Adds an address.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds an address.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\CustomerBundle\Model\Address"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while address creation"
     *     )
     * )
     *
     *
     * @Rest\Post("/addresses.{_format}", name="post_address")
     *
     * @param Request $request A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function postAddressAction(Request $request)
    {
        return $this->handleWriteAddress($request);
    }

    /**
     * Updates an address.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates an address.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\CustomerBundle\Model\Address"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while address creation"
     *     )
     * )
     *
     *
     * @Rest\Put("/addresses/{id}.{_format}", name="put_address")
     *
     * @param int     $id      An Address identifier
     * @param Request $request A Symfony request
     *
     * @return FOSRestView|FormInterface
     */
    public function putAddressAction($id, Request $request)
    {
        return $this->handleWriteAddress($request, $id);
    }

    /**
     * Deletes an address.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes an address.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when customer is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while address deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find address"
     *     )
     * )
     *
     *
     * @Rest\Delete("/addresses/{id}.{_format}", name="delete_address")
     *
     * @param int $id An Address identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAddressAction($id)
    {
        $address = $this->getAddress($id);

        try {
            $this->addressManager->delete($address);
        } catch (\Exception $e) {
            return FOSRestView::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Retrieves address with id $id or throws an exception if it doesn't exist.
     *
     * @param int $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return AddressInterface
     */
    protected function getAddress($id)
    {
        $address = $this->addressManager->findOneBy(['id' => $id]);

        if (null === $address) {
            throw new NotFoundHttpException(sprintf('Address (%d) not found', $id));
        }

        return $address;
    }

    /**
     * Write an address, this method is used by both POST and PUT action methods.
     *
     * @param Request  $request Symfony request
     * @param int|null $id      An Address identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
     */
    protected function handleWriteAddress($request, $id = null)
    {
        $address = $id ? $this->getAddress($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_customer_api_form_address', $address, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $this->addressManager->save($address);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            // simplify when dropping FOSRest < 2.1
            if (method_exists($context, 'enableMaxDepth')) {
                $context->enableMaxDepth();
            } else {
                $context->setMaxDepth(10);
            }

            $view = FOSRestView::create($address);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
