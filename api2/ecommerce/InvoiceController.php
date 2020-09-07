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

namespace Sonata\InvoiceBundle\Controller;

use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Transformer\InvoiceTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InvoiceController extends Controller
{
    /**
     * @throws \RuntimeException
     */
    public function indexAction(): void
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * @param string $reference
     *
     * @throws AccessDeniedException
     *
     * @return Response
     */
    public function viewAction($reference)
    {
        $order = $this->getOrderManager()->findOneBy(['reference' => $reference]);

        if (null === $order) {
            throw new AccessDeniedException();
        }

        $this->checkAccess($order->getCustomer());

        $invoice = $this->getInvoiceManager()->findOneBy(['reference' => $reference]);

        if (null === $invoice) {
            $invoice = $this->getInvoiceManager()->create();

            $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);
            $this->getInvoiceManager()->save($invoice);
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('invoice_view_title', [], 'SonataInvoiceBundle'));

        return $this->render('@SonataInvoice/Invoice/view.html.twig', [
            'invoice' => $invoice,
            'order' => $order,
        ]);
    }

    /**
     * @param string $reference
     *
     * @throws \RuntimeException
     */
    public function downloadAction($reference): void
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * Checks that the current logged in user has access to given invoice.
     *
     * @param CustomerInterface $customer The linked customer
     *
     * @throws AccessDeniedException
     */
    protected function checkAccess(CustomerInterface $customer): void
    {
        if (!($user = $this->getUser())
            || !$customer->getUser()
            || $customer->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @return InvoiceManagerInterface
     */
    protected function getInvoiceManager()
    {
        return $this->get('sonata.invoice.manager');
    }

    /**
     * @return OrderManagerInterface
     */
    protected function getOrderManager()
    {
        return $this->get('sonata.order.manager');
    }

    /**
     * @return InvoiceTransformer
     */
    protected function getInvoiceTransformer()
    {
        return $this->get('sonata.payment.transformer.invoice');
    }
}
