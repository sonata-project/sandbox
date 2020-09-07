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
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
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
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for addresses list pagination (1-indexed)")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of addresses by page")
     * @Rest\QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Sort specification for the resultset (key is field, value is direction")
     * @Rest\QueryParam(name="customer", requirements="\d+", nullable=true, strict=true, description="Filter on customer id")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
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
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Address identifier
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
     * @param Request $request Symfony request
     *
     * @return View|FormInterface
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
     * @param string  $id      Address identifier
     * @param Request $request Symfony request
     *
     * @return View|FormInterface
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
     * @param string $id Address identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deleteAddressAction($id)
    {
        $address = $this->getAddress($id);

        try {
            $this->addressManager->delete($address);
        } catch (\Exception $e) {
            return View::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Retrieves address with identifier $id or throws an exception if it doesn't exist.
     *
     * @param string $id Address identifier
     *
     * @throws NotFoundHttpException
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
     * @param Request     $request Symfony request
     * @param string|null $id      Address identifier
     *
     * @return View|FormInterface
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

            $view = View::create($address);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
