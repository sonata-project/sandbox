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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Customer identifier"}
     *  },
     *  output={"class"="Sonata\Component\Customer\CustomerInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when customer is not found"
     *  }
     * )
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
     * @ApiDoc(
     *  input={"class"="sonata_customer_api_form_customer", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\CustomerBundle\Model\Customer", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while customer creation",
     *  }
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Customer identifier"}
     *  },
     *  input={"class"="sonata_customer_api_form_customer", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\CustomerBundle\Model\Customer", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while customer update",
     *      404="Returned when unable to find customer"
     *  }
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Customer identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when customer is successfully deleted",
     *      400="Returned when an error has occurred while customer deletion",
     *      404="Returned when unable to find customer"
     *  }
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Customer identifier"}
     *  },
     *  output={"class"="Sonata\Component\Order\OrderInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when customer is not found"
     *  }
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Customer identifier"}
     *  },
     *  output={"class"="Sonata\Component\Customer\AddressInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when customer is not found"
     *  }
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Customer identifier"}
     *  },
     *  input={"class"="sonata_customer_api_form_address", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\CustomerBundle\Model\Address", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while address creation",
     *  }
     * )
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
