<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use Application\Sonata\CustomerBundle\Entity\Address;
use Application\Sonata\CustomerBundle\Entity\Customer;
use Application\Sonata\InvoiceBundle\Entity\Invoice;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Application\Sonata\OrderBundle\Entity\OrderElement;
use Application\Sonata\PaymentBundle\Entity\Transaction;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductDefinition;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\CustomerBundle\Entity\BaseCustomer;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\PaymentBundle\Entity\BaseTransaction;
use Sonata\ProductBundle\Entity\BaseProduct;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\Sonata\OrderBundle\Entity\Order;
use Application\Sonata\ProductBundle\Entity\Delivery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Category fixtures loader.
 *
 * @author Hugo Briand <briand@ekino.com>
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
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
        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket = new Basket();
        $basket->setCurrency($currency);
        $basket->setProductPool($this->getProductPool());

        $nbCustomers = 100;

        $products = array(
            $this->getReference('php_plush_blue_goodie_product'),
            $this->getReference('php_plush_green_goodie_product'),
            $this->getReference('travel_japan_small_product'),
            $this->getReference('travel_japan_medium_product'),
            $this->getReference('travel_japan_large_product'),
            $this->getReference('travel_japan_extra_large_product'),
            $this->getReference('travel_quebec_small_product'),
            $this->getReference('travel_quebec_medium_product'),
            $this->getReference('travel_quebec_large_product'),
            $this->getReference('travel_quebec_extra_large_product'),
            //$this->getReference('travel_london_small_product'),
            //$this->getReference('travel_london_medium_product'),
            //$this->getReference('travel_london_large_product'),
            //$this->getReference('travel_london_extra_large_product'),
            $this->getReference('travel_paris_small_product'),
            $this->getReference('travel_paris_medium_product'),
            $this->getReference('travel_paris_large_product'),
            $this->getReference('travel_paris_extra_large_product'),
            $this->getReference('travel_switzerland_small_product'),
            $this->getReference('travel_switzerland_medium_product'),
            $this->getReference('travel_switzerland_large_product'),
            $this->getReference('travel_switzerland_extra_large_product'),
        );

        for ($i = 1; $i <= $nbCustomers; $i++) {
            $customer = $this->generateCustomer($manager, $i);

            $customerProducts = array();

            $orderProductsKeys = array_rand($products, rand(1, count($products)));

            if (is_array($orderProductsKeys)) {
                foreach ($orderProductsKeys as $orderProductKey) {
                    $customerProducts[] = $products[$orderProductKey];
                }
            } else {
                $customerProducts = array($products[$orderProductsKeys]);
            }

            $order = $this->createOrder($basket, $customer, $customerProducts, $manager, $i);


            $this->createTransaction($order, $manager);

            $this->createInvoice($order, $manager);

            if (!($i % 10)) {
                $manager->flush();
            }
        }

        $manager->flush();
    }

    /**
     * Creates a fake Order.
     *
     * @param BasketInterface $basket
     * @param Customer        $customer
     * @param array           $products
     * @param ObjectManager   $manager
     * @param int             $pos
     *
     * @return OrderInterface
     */
    protected function createOrder(BasketInterface $basket, Customer $customer, array $products, ObjectManager $manager, $pos)
    {
        $orderElements = array();
        $totalExcl     = 0;
        $totalInc      = 0;

        $order = new Order();

        // customer
        $order->setCustomer($customer);
        $order->setLocale($customer->getLocale());
        $order->setUsername($customer->getFullname());
        $order->setReference(sprintf('%02d%02d%02d%06d',
            2013, // @todo: need to improve the date generation
            7,
            1,
            $pos
        ));

        // Billing
        $customerBillingAddressAddresses = $customer->getAddressesByType(BaseAddress::TYPE_BILLING);
        $order->setBillingAddress1($customerBillingAddressAddresses[0]->getAddress1());
        $order->setBillingAddress2($customerBillingAddressAddresses[0]->getAddress2());
        $order->setBillingAddress3($customerBillingAddressAddresses[0]->getAddress3());
        $order->setBillingCity($customerBillingAddressAddresses[0]->getCity());
        $order->setBillingCountryCode($customerBillingAddressAddresses[0]->getCountryCode());
        $order->setBillingEmail($customer->getEmail());
        $order->setBillingName($customer->getFullname());
        $order->setBillingPhone($customerBillingAddressAddresses[0]->getPhone());
        $order->setBillingFax($customer->getFaxNumber());
        $order->setBillingMobile($customer->getMobileNumber());
        $order->setBillingPostcode($customerBillingAddressAddresses[0]->getPostcode());

        // Shipping
        $customerDeliveryAddressAddresses = $customer->getAddressesByType(BaseAddress::TYPE_DELIVERY);
        $order->setShippingAddress1($customerDeliveryAddressAddresses[0]->getAddress1());
        $order->setShippingAddress2($customerDeliveryAddressAddresses[0]->getAddress2());
        $order->setShippingAddress3($customerDeliveryAddressAddresses[0]->getAddress3());
        $order->setShippingCity($customerDeliveryAddressAddresses[0]->getCity());
        $order->setShippingCountryCode($customerDeliveryAddressAddresses[0]->getCountryCode());
        $order->setShippingEmail($customer->getEmail());
        $order->setShippingName($customer->getFullname());
        $order->setShippingPhone($customerDeliveryAddressAddresses[0]->getPhone());
        $order->setShippingFax($customer->getFaxNumber());
        $order->setShippingMobile($customer->getMobileNumber());
        $order->setShippingPostcode($customerDeliveryAddressAddresses[0]->getPostcode());

        // Delivery
        $order->setDeliveryMethod('free'); // @todo : change Delivery method integration
        $order->setDeliveryCost(rand(5, 40));
        $order->setDeliveryVat(20);
        $order->setDeliveryStatus(array_rand(Delivery::getStatusList()));

        $currency = new Currency();
        $currency->setLabel('EUR');

        // Payment
        $order->setCurrency($currency);
        $order->setPaymentMethod('free');    // @todo : change Payment method integration
        $order->setPaymentStatus(array_rand(BaseTransaction::getStatusList()));

        // OrderElements
        foreach ($products as $product) {
            $orderElement = $this->createOrderElement($basket, $product);

            $orderElement->setOrder($order);

            $orderElements[] = $orderElement;

            $totalExcl += $orderElement->getUnitPrice(false);
            $totalInc  += $orderElement->getUnitPrice(true);
        }

        $order->setOrderElements($orderElements);

        $order->setTotalExcl($totalExcl);
        $order->setTotalInc($totalInc);

        $order->setStatus(array_rand(BaseOrder::getStatusList()));

        if (OrderInterface::STATUS_VALIDATED == $order->getStatus()) {
            $order->setValidatedAt(new \DateTime());
        }

        $manager->persist($order);

        return $order;
    }

    /**
     * Creates an OrderElement from a given Product.
     *
     * @param BasketInterface $basket  A basket instance
     * @param BaseProduct     $product A product instance
     *
     * @return OrderElement
     */
    protected function createOrderElement(BasketInterface $basket, BaseProduct $product)
    {
        $productProvider = $this->getProductPool()->getProvider($product);
        $productManager = $this->getProductPool()->getManager($product);

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = $productProvider->createBasketElement($product);
        $basketElement->setProductDefinition($productDefinition);

        $basket->addBasketElement($basketElement);

        $productProvider->updateComputationPricesFields($basket, $basketElement, $product);

        $orderElement = $productProvider->createOrderElement($basketElement);

        return $orderElement;
    }

    /**
     * Creates an Invoice for a given Order.
     *
     * @param OrderInterface $order
     * @param ObjectManager  $manager
     */
    protected function createInvoice(OrderInterface $order, ObjectManager $manager)
    {
        $invoice = new Invoice();

        $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);

        if ($order->isValidated()) {
            $invoice->setStatus(InvoiceInterface::STATUS_PAID);
        }

        $manager->persist($invoice);
    }

    /**
     * Generates a Customer with his addresses.
     *
     * @param ObjectManager $manager
     * @param int $i Random number to avoid username collision
     *
     * @return Customer
     */
    protected function generateCustomer(ObjectManager $manager, $i)
    {
        $faker = $this->getFaker();

        $firstName = $faker->firstName();
        $lastName = $faker->lastName();
        $email = $faker->email();
        $username = $faker->userName();

        if (0 === $i % 50 && $this->hasReference('customer-johndoe')) {
            return $this->getReference('customer-johndoe');
        }

        // Customer
        $customer = new Customer();
        $customer->setTitle(array_rand(array(BaseCustomer::TITLE_MLLE, BaseCustomer::TITLE_MME, BaseCustomer::TITLE_MR)));
        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        $customer->setEmail($email);
        $customer->setBirthDate($faker->datetime());
        $customer->getBirthPlace($faker->city());
        $customer->setPhoneNumber($faker->phoneNumber());
        $customer->setMobileNumber($faker->phoneNumber());
        $customer->setFaxNumber($faker->phoneNumber());
        $customer->setLocale('fr');
        $customer->setIsFake(true);

        // Customer billing address
        $customerBillingAddress = new Address();
        $customerBillingAddress->setType(BaseAddress::TYPE_BILLING);
        $customerBillingAddress->setCustomer($customer);
        $customerBillingAddress->setCurrent(true);
        $customerBillingAddress->setName('My billing address');
        $customerBillingAddress->setFirstname($customer->getFirstname());
        $customerBillingAddress->setLastname($customer->getLastname());
        $customerBillingAddress->setAddress1($faker->address());
        $customerBillingAddress->setPostcode($faker->postcode());
        $customerBillingAddress->setCity($faker->city());
        $customerBillingAddress->setCountryCode(0 === $i % 50 ? 'FR' : $faker->countryCode());
        $customerBillingAddress->setPhone($faker->phoneNumber());

        // Customer contact address
        $customerContactAddress = new Address();
        $customerContactAddress->setType(BaseAddress::TYPE_CONTACT);
        $customerContactAddress->setCustomer($customer);
        $customerContactAddress->setCurrent(true);
        $customerContactAddress->setName('My contact address');
        $customerContactAddress->setFirstname($customer->getFirstname());
        $customerContactAddress->setLastname($customer->getLastname());
        $customerContactAddress->setAddress1($faker->address());
        $customerContactAddress->setPostcode($faker->postcode());
        $customerContactAddress->setCity($faker->city());
        $customerContactAddress->setCountryCode(0 === $i % 50 ? 'FR' : $faker->countryCode());
        $customerContactAddress->setPhone($customer->getPhoneNumber());

        // Customer delivery address
        $customerDeliveryAddress = new Address();
        $customerDeliveryAddress->setType(BaseAddress::TYPE_DELIVERY);
        $customerDeliveryAddress->setCustomer($customer);
        $customerDeliveryAddress->setCurrent(true);
        $customerDeliveryAddress->setName('My delivery address');
        $customerDeliveryAddress->setFirstname($customer->getFirstname());
        $customerDeliveryAddress->setLastname($customer->getLastname());
        $customerDeliveryAddress->setAddress1($faker->address());
        $customerDeliveryAddress->setPostcode($faker->postcode());
        $customerDeliveryAddress->setCity($faker->city());
        $customerDeliveryAddress->setCountryCode(0 === $i % 50 ? 'FR' : $faker->countryCode());
        $customerDeliveryAddress->setPhone($faker->phoneNumber());

        $customer->addAddress($customerBillingAddress);
        $customer->addAddress($customerContactAddress);
        $customer->addAddress($customerDeliveryAddress);

        // User
        if (0 === $i % 10) {
            $user = $this->getReference('user-johndoe');
            $this->setReference('customer-johndoe', $customer);
        } else {
            /** @var \Sonata\UserBundle\Model\User $user */
            $user = new User();
            $user->setUsername($i . '-' . $username);
            $user->setUsernameCanonical($i . '-' . $username);
            $user->setEmail($i.'_'.$email);
            $user->setEmailCanonical($email);
            $user->setPlainPassword('customer');
        }

        $customer->setUser($user);

        $manager->persist($customerBillingAddress);
        $manager->persist($customerContactAddress);
        $manager->persist($customerDeliveryAddress);
        $manager->persist($user);
        $manager->persist($customer);

        return $customer;
    }

    protected function createTransaction(OrderInterface $order, ObjectManager $manager)
    {
        $transaction = new Transaction();

        $transaction->setState(1);
        $transaction->setOrder($order);
        $transaction->setStatusCode($order->getPaymentStatus());
        $transaction->setPaymentCode($order->getPaymentMethod());

        $manager->persist($transaction);
    }

    /**
     * Get Product Pool.
     *
     * @return \Sonata\Component\Product\Pool
     */
    protected function getProductPool()
    {
        return $this->container->get('sonata.product.pool');
    }

    /**
     * Get Invoice Transformer.
     *
     * @return \Sonata\Component\Transformer\InvoiceTransformer
     */
    protected function getInvoiceTransformer()
    {
        return $this->container->get('sonata.payment.transformer.invoice');
    }

    /**
     * Get Faker.
     *
     * @return Generator
     */
    protected function getFaker()
    {
        return $this->container->get('faker.generator');
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
        return 13;
    }
}
