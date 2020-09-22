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

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use App\Entity\Commerce\Address;
use App\Entity\Commerce\Customer;
use App\Entity\Commerce\Delivery;
use App\Entity\Commerce\Invoice;
use App\Entity\Commerce\Order;
use App\Entity\Commerce\Transaction;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Transformer\InvoiceTransformer;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\CustomerBundle\Entity\BaseCustomer;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\PaymentBundle\Entity\BaseTransaction;
use Sonata\ProductBundle\Entity\BaseProduct;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Category fixtures loader.
 *
 * @author Hugo Briand <briand@ekino.com>
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class LoadOrderData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ArrayCollection
     */
    protected $orderElements;

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var InvoiceTransformer
     */
    protected $invoiceTransformer;

    /**
     * @var Pool
     */
    protected $productPool;

    /**
     * @var ContainerBagInterface
     */
    protected $parameterBag;

    public function __construct(
        Generator $faker,
        InvoiceTransformer $invoiceTransformer,
        Pool $productPool,
        ContainerBagInterface $parameterBag
    ) {
        $this->faker = $faker;
        $this->invoiceTransformer = $invoiceTransformer;
        $this->productPool = $productPool;
        $this->parameterBag = $parameterBag;
    }

    public function getOrder(): int
    {
        return 9;
    }

    public function load(ObjectManager $manager): void
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $basket = new Basket();
        $basket->setCurrency($currency);
        $basket->setProductPool($this->productPool);

        $products = $this->getProductList();

        $nbCustomers = $this->parameterBag->has('sonata.fixtures.customer.fake') ? (int) $this->parameterBag->get('sonata.fixtures.customer.fake') : 20;

        for ($i = 1; $i <= $nbCustomers; ++$i) {
            $customer = $this->generateCustomer($manager, $i);

            $customerProducts = [];

            $orderProductsKeys = array_rand($products, random_int(1, \count($products)));

            if (\is_array($orderProductsKeys)) {
                foreach ($orderProductsKeys as $orderProductKey) {
                    $customerProducts[] = $products[$orderProductKey];
                }
            } else {
                $customerProducts = [$products[$orderProductsKeys]];
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

    protected function createOrder(BasketInterface $basket, Customer $customer, array $products, ObjectManager $manager, int $pos): Order
    {
        $orderElements = [];
        $totalExcl = 0;
        $totalInc = 0;

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
        $customerBillingAddressAddresses = $customerBillingAddressAddresses->current();
        $order->setBillingAddress1($customerBillingAddressAddresses->getAddress1());
        $order->setBillingAddress2($customerBillingAddressAddresses->getAddress2());
        $order->setBillingAddress3($customerBillingAddressAddresses->getAddress3());
        $order->setBillingCity($customerBillingAddressAddresses->getCity());
        $order->setBillingCountryCode($customerBillingAddressAddresses->getCountryCode());
        $order->setBillingEmail($customer->getEmail());
        $order->setBillingName($customer->getFullname());
        $order->setBillingPhone($customerBillingAddressAddresses->getPhone());
        $order->setBillingFax($customer->getFaxNumber());
        $order->setBillingMobile($customer->getMobileNumber());
        $order->setBillingPostcode($customerBillingAddressAddresses->getPostcode());

        // Shipping
        $customerDeliveryAddressAddresses = $customer->getAddressesByType(BaseAddress::TYPE_DELIVERY);
        $customerDeliveryAddressAddresses = $customerDeliveryAddressAddresses->current();
        $order->setShippingAddress1($customerDeliveryAddressAddresses->getAddress1());
        $order->setShippingAddress2($customerDeliveryAddressAddresses->getAddress2());
        $order->setShippingAddress3($customerDeliveryAddressAddresses->getAddress3());
        $order->setShippingCity($customerDeliveryAddressAddresses->getCity());
        $order->setShippingCountryCode($customerDeliveryAddressAddresses->getCountryCode());
        $order->setShippingEmail($customer->getEmail());
        $order->setShippingName($customer->getFullname());
        $order->setShippingPhone($customerDeliveryAddressAddresses->getPhone());
        $order->setShippingFax($customer->getFaxNumber());
        $order->setShippingMobile($customer->getMobileNumber());
        $order->setShippingPostcode($customerDeliveryAddressAddresses->getPostcode());

        // Delivery
        $order->setDeliveryMethod('free'); // @todo : change Delivery method integration
        $order->setDeliveryCost(random_int(5, 40));
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
            $totalInc += $orderElement->getUnitPrice(true);
        }

        $order->setOrderElements($orderElements);

        $order->setTotalExcl($totalExcl);
        $order->setTotalInc($totalInc);

        $order->setStatus(array_rand(BaseOrder::getStatusList()));

        if (OrderInterface::STATUS_VALIDATED === $order->getStatus()) {
            $order->setValidatedAt(new \DateTime());
        }

        $manager->persist($order);

        return $order;
    }

    protected function createOrderElement(BasketInterface $basket, BaseProduct $product): OrderElementInterface
    {
        $productProvider = $this->productPool->getProvider($product);
        $productManager = $this->productPool->getManager($product);

        $productDefinition = new ProductDefinition($productProvider, $productManager);

        $basketElement = $productProvider->createBasketElement($product);
        $basketElement->setProductDefinition($productDefinition);

        $basket->addBasketElement($basketElement);

        $productProvider->updateComputationPricesFields($basket, $basketElement, $product);

        $orderElement = $productProvider->createOrderElement($basketElement);

        return $orderElement;
    }

    protected function createInvoice(OrderInterface $order, ObjectManager $manager): Invoice
    {
        $invoice = new Invoice();

        $this->invoiceTransformer->transformFromOrder($order, $invoice);

        if ($order->isValidated()) {
            $invoice->setStatus(InvoiceInterface::STATUS_PAID);
        }

        $manager->persist($invoice);

        return $invoice;
    }

    /**
     * Generates a Customer with his addresses.
     *
     * @param int $i Random number to avoid username collision
     */
    protected function generateCustomer(ObjectManager $manager, int $i): Customer
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $email = $this->faker->email();
        $username = $this->faker->userName();

        if (0 === $i % 50 && $this->hasReference('customer-johndoe')) {
            return $this->getReference('customer-johndoe');
        }

        // Customer
        $customer = new Customer();
        $customer->setTitle(array_rand([BaseCustomer::TITLE_MLLE, BaseCustomer::TITLE_MME, BaseCustomer::TITLE_MR]));
        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        $customer->setEmail($email);
        $customer->setBirthDate($this->faker->datetime());
        $customer->getBirthPlace($this->faker->city());
        $customer->setPhoneNumber($this->faker->phoneNumber());
        $customer->setMobileNumber($this->faker->phoneNumber());
        $customer->setFaxNumber($this->faker->phoneNumber());
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
        $customerBillingAddress->setAddress1($this->faker->address());
        $customerBillingAddress->setPostcode($this->faker->postcode());
        $customerBillingAddress->setCity($this->faker->city());
        $customerBillingAddress->setCountryCode(0 === $i % 50 ? 'FR' : $this->faker->countryCode());
        $customerBillingAddress->setPhone($this->faker->phoneNumber());

        // Customer contact address
        $customerContactAddress = new Address();
        $customerContactAddress->setType(BaseAddress::TYPE_CONTACT);
        $customerContactAddress->setCustomer($customer);
        $customerContactAddress->setCurrent(true);
        $customerContactAddress->setName('My contact address');
        $customerContactAddress->setFirstname($customer->getFirstname());
        $customerContactAddress->setLastname($customer->getLastname());
        $customerContactAddress->setAddress1($this->faker->address());
        $customerContactAddress->setPostcode($this->faker->postcode());
        $customerContactAddress->setCity($this->faker->city());
        $customerContactAddress->setCountryCode(0 === $i % 50 ? 'FR' : $this->faker->countryCode());
        $customerContactAddress->setPhone($customer->getPhoneNumber());

        // Customer delivery address
        $customerDeliveryAddress = new Address();
        $customerDeliveryAddress->setType(BaseAddress::TYPE_DELIVERY);
        $customerDeliveryAddress->setCustomer($customer);
        $customerDeliveryAddress->setCurrent(true);
        $customerDeliveryAddress->setName('My delivery address');
        $customerDeliveryAddress->setFirstname($customer->getFirstname());
        $customerDeliveryAddress->setLastname($customer->getLastname());
        $customerDeliveryAddress->setAddress1($this->faker->address());
        $customerDeliveryAddress->setPostcode($this->faker->postcode());
        $customerDeliveryAddress->setCity($this->faker->city());
        $customerDeliveryAddress->setCountryCode(0 === $i % 50 ? 'FR' : $this->faker->countryCode());
        $customerDeliveryAddress->setPhone($this->faker->phoneNumber());

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
            $user->setUsername($i.'-'.$username);
            $user->setUsernameCanonical($i.'-'.$username);
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

    protected function createTransaction(OrderInterface $order, ObjectManager $manager): Transaction
    {
        $transaction = new Transaction();

        $transaction->setState(1);
        $transaction->setOrder($order);
        $transaction->setStatusCode($order->getPaymentStatus());
        $transaction->setPaymentCode($order->getPaymentMethod());

        $manager->persist($transaction);

        return $transaction;
    }

    protected function getProductList()
    {
        return [
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
            $this->getReference('travel_london_small_product'),
            $this->getReference('travel_london_medium_product'),
            $this->getReference('travel_london_large_product'),
            $this->getReference('travel_london_extra_large_product'),
            $this->getReference('travel_paris_small_product'),
            $this->getReference('travel_paris_medium_product'),
            $this->getReference('travel_paris_large_product'),
            $this->getReference('travel_paris_extra_large_product'),
            $this->getReference('travel_switzerland_small_product'),
            $this->getReference('travel_switzerland_medium_product'),
            $this->getReference('travel_switzerland_large_product'),
            $this->getReference('travel_switzerland_extra_large_product'),
        ];
    }
}
