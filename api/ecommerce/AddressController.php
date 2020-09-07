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
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Address identifier"}
     *  },
     *  output={"class"="Sonata\Component\Customer\AddressInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when address is not found"
     *  }
     * )
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
     * @ApiDoc(
     *  input={"class"="sonata_customer_api_form_address", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\CustomerBundle\Model\Address", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while address creation",
     *  }
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Address identifier"}
     *  },
     *  input={"class"="sonata_customer_api_form_address", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\CustomerBundle\Model\Address", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while address creation",
     *  }
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Address identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when customer is successfully deleted",
     *      400="Returned when an error has occurred while address deletion",
     *      404="Returned when unable to find address"
     *  }
     * )
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
