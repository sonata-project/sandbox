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
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CustomerController
{
    /**
     * @var AddressManagerInterface
     */
    protected $addressManager;

    /**
     * @var CustomerManagerInterface
     */
    protected $customerManager;

    /**
     * @var OrderManagerInterface
     */
    protected $orderManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(CustomerManagerInterface $customerManager, OrderManagerInterface $orderManager, AddressManagerInterface $addressManager, FormFactoryInterface $formFactory)
    {
        $this->customerManager = $customerManager;
        $this->orderManager = $orderManager;
        $this->addressManager = $addressManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a paginated list of customers.
     *
     * @Operation(
     *     tags={""},
     *     summary="Returns a paginated list of customers.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for customers list pagination (1-indexed)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of customers by page",
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
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\DatagridBundle\Pager\PagerInterface"))
     *     )
     * )
     *
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for customers list pagination (1-indexed)")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of customers by page")
     * @Rest\QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Sort specification for the resultset (key is field, value is direction")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getCustomersAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'is_fake' => '',
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

        return $this->customerManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific customer.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific customer.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Customer\CustomerInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when customer is not found"
     *     )
     * )
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Customer identifier
     *
     * @return CustomerInterface
     */
    public function getCustomerAction($id)
    {
        return $this->getCustomer($id);
    }

    /**
     * Adds a customer.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a customer.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\CustomerBundle\Model\Customer"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while customer creation"
     *     )
     * )
     *
     *
     * @param Request $request Symfony request
     *
     * @return View|FormInterface
     */
    public function postCustomerAction(Request $request)
    {
        return $this->handleWriteCustomer($request);
    }

    /**
     * Updates a customer.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a customer.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\CustomerBundle\Model\Customer"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while customer update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find customer"
     *     )
     * )
     *
     *
     * @param string  $id      Customer identifier
     * @param Request $request Symfony request
     *
     * @return View|FormInterface
     */
    public function putCustomerAction($id, Request $request)
    {
        return $this->handleWriteCustomer($request, $id);
    }

    /**
     * Deletes a customer.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a customer.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when customer is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while customer deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find customer"
     *     )
     * )
     *
     *
     * @param string $id Customer identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deleteCustomerAction($id)
    {
        $customer = $this->getCustomer($id);

        try {
            $this->customerManager->delete($customer);
        } catch (\Exception $e) {
            return View::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Retrieves a specific customer's orders.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific customer's orders.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Order\OrderInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when customer is not found"
     *     )
     * )
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Customer identifier
     *
     * @return array
     */
    public function getCustomerOrdersAction($id)
    {
        $customer = $this->getCustomer($id);

        return $this->orderManager->findBy(['customer' => $customer]);
    }

    /**
     * Retrieves a specific customer's addresses.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific customer's addresses.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\Component\Customer\AddressInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when customer is not found"
     *     )
     * )
     *
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Customer identifier
     *
     * @return array
     */
    public function getCustomerAddressesAction($id)
    {
        return $this->getCustomer($id)->getAddresses();
    }

    /**
     * Adds a customer address.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a customer address.",
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
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string  $id      Customer identifier
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return AddressInterface
     */
    public function postCustomerAddressAction($id, Request $request)
    {
        $customer = $id ? $this->getCustomer($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_customer_api_form_address', null, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $address->setCustomer($customer);

            $this->addressManager->save($address);

            return $address;
        }

        return $form;
    }

    /**
     * Write a customer, this method is used by both POST and PUT action methods.
     *
     * @param Request     $request Symfony request
     * @param string|null $id      Customer identifier
     *
     * @return View|FormInterface
     */
    protected function handleWriteCustomer($request, $id = null)
    {
        $customer = $id ? $this->getCustomer($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_customer_api_form_customer', $customer, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $form->getData();
            $this->customerManager->save($customer);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            // simplify when dropping FOSRest < 2.1
            if (method_exists($context, 'enableMaxDepth')) {
                $context->enableMaxDepth();
            } else {
                $context->setMaxDepth(10);
            }

            $view = View::create($customer);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }

    /**
     * Retrieves customer with identifier $id or throws an exception if it doesn't exist.
     *
     * @param string $id Customer identifier
     *
     * @throws NotFoundHttpException
     *
     * @return CustomerInterface
     */
    protected function getCustomer($id)
    {
        $customer = $this->customerManager->findOneBy(['id' => $id]);

        if (null === $customer) {
            throw new NotFoundHttpException(sprintf('Customer (%d) not found', $id));
        }

        return $customer;
    }
}
