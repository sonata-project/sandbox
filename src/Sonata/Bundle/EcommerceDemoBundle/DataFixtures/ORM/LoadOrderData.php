<?php
namespace Sonata\Bundle\EcommerceDemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\Sonata\OrderBundle\Entity\Order;
use Application\Sonata\ProductBundle\Entity\Delivery;
use Sonata\Component\Payment\TransactionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 */
class LoadOrderData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ArrayCollection
     */
    protected $orderElements;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Unused for now, faker deals with populating the orders
        // however we keep the file as it might get in handy later
        //$this->createOrders();
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        // Right after products
        return 51;
    }

    /**
     * Creates the order fixtures
     */
    public function createOrders()
    {
        $statuses = array_rand(Order::getStatusList(), 10);

        foreach ($statuses as $key => $status) {
            $this->createOrder($key, $status);
        }
    }

    /**
     * Creates an order
     *
     * @param int $key
     * @param int $status
     */
    public function createOrder($key, $status)
    {
        $customer = $this->getReference('customer');

        $order = new Order();
        $order->setReference('order-'.$status.'-'.$key);

        $order->setBillingAddress1('address1');
        $order->setBillingCity('city');
        $order->setBillingCountryCode('EN');
        $order->setBillingEmail('email@email.com');
        $order->setBillingName('name');
        $order->setBillingPhone('0123456789');
        $order->setBillingPostcode('12345');

        $order->setCurrency('EUR');
        $order->setCustomer($customer);

        $order->setDeliveryCost(42.0);
        $order->setDeliveryMethod('delivery');
        $order->setDeliveryStatus(array_rand(Delivery::getStatusList()));

        $order->setLocale('EN');
        $order->setOrderElements($this->getOrderElements());
        $order->setPaymentMethod('payment');
        $order->setPaymentStatus(array_rand(TransactionInterface::getStatusList()));

        $order->setShippingAddress1('address1');
        $order->setShippingCity('city');
        $order->setShippingCountryCode('EN');
        $order->setShippingEmail('email@email.com');
        $order->setShippingName('name');
        $order->setShippingPhone('0123456789');
        $order->setShippingPostcode('12345');

        $order->setStatus($status);

        $order->setTotalExcl(42.0);
        $order->setTotalInc(42.0);
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderElements()
    {
        if (null !== $this->orderElements) {
            return $this->orderElements;
        }

    }

    /**
     * @return Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }
}