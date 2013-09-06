<?php

namespace Sonata\Bundle\EcommerceDemoBundle\DataFixtures\ORM;

use Application\Sonata\ProductBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\Component\Product\ProductManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadProductData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function getOrder()
    {
        return 50;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
    }


    /**
     * @return MediaInterface
     */
    public function getImage()
    {
        return $this->getReference('sonata-media-0');
    }

    /**
     * @return \Sonata\Component\Product\CategoryManagerInterface
     */
    public function getCategoryManager()
    {
        return $this->container->get('sonata.category.manager');
    }

    /**
     * @return \Sonata\Component\Product\DeliveryManagerInterface
     */
    public function getDeliveryManager()
    {
        return $this->container->get('sonata.delivery.manager');
    }

    /**
     * @return \Sonata\ProductBundle\Entity\ProductCategoryManager
     */
    public function getProductCategoryManager()
    {

        return $this->container->get('sonata.product_category.product');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }
}