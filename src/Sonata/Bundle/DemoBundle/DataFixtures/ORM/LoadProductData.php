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

use Application\Sonata\ProductBundle\Entity\Delivery;
use Application\Sonata\ProductBundle\Entity\Goodie;
use Application\Sonata\ProductBundle\Entity\Package;
use Application\Sonata\ProductBundle\Entity\ProductCategory;
use Application\Sonata\ProductBundle\Entity\ProductCollection;
use Application\Sonata\ProductBundle\Entity\Training;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Product fixtures loader.
 *
 * @author Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class LoadProductData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /*
         * @todo
         *  - add product variations
         *  - check licences for used images
         */
        $plushesCategory = $this->getPlushesCategory();
        $sonataCategory  = $this->getSonataTrainingCategory();
        $mugCategory     = $this->getMugCategory();
        $clothesCategory = $this->getClothesCategory();

        $phpCollection = $this->getPhpCollection();

        // Goodies products
        $phpPlush = new Goodie();
        $phpPlush->setSku('PHP_PLUSH');
        $phpPlush->setName('PHP plush');
        $phpPlush->setSlug('php-plush');
        $phpPlush->setDescription('The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier: Vince evolved the logo from the three letters into an animal.');
        $phpPlush->setRawDescription('The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier: Vince evolved the logo from the three letters into an animal.');
        $phpPlush->setPrice(29.99);
        $phpPlush->setStock(2000);
        $phpPlush->setVat(0);
        $phpPlush->setEnabled(true);
        $manager->persist($phpPlush);
        $this->setReference('php_plush_goodie_product', $phpPlush);

        $this->addMediaToProduct(__DIR__.'/../data/files/elephpant.png', 'PHP elePHPant', 'PHP elePHPant', $phpPlush);
        $this->addProductToCategory($phpPlush, $plushesCategory, $manager);
        $this->addProductDeliveries($phpPlush, $manager);
        $this->addProductToCollection($phpPlush, $phpCollection, $manager);

        $phpMug = new Goodie();
        $phpMug->setSku('PHP_MUG');
        $phpMug->setName('PHP mug');
        $phpMug->setSlug('php-mug');
        $phpMug->setDescription('You love coffee and PHP ? This mug is for you.');
        $phpMug->setRawDescription('You love coffee and PHP ? This mug is for you.');
        $phpMug->setPrice(9.99);
        $phpMug->setStock(10000);
        $phpMug->setVat(0);
        $phpMug->setEnabled(true);
        $manager->persist($phpMug);
        $this->setReference('php_mug_goodie_product', $phpMug);

        $this->addMediaToProduct(__DIR__.'/../data/files/php_mug.jpg', 'PHP mug', 'PHP mug', $phpMug);
        $this->addProductToCategory($phpMug, $mugCategory, $manager);
        $this->addProductDeliveries($phpMug, $manager);
        $this->addProductToCollection($phpMug, $phpCollection, $manager);

        $phpTeeShirt = new Goodie();
        $phpTeeShirt->setSku('PHP_TSHIRT');
        $phpTeeShirt->setName('PHP tee-shirt');
        $phpTeeShirt->setSlug('php-t-shirt');
        $phpTeeShirt->setDescription('A nice PHP tee-shirt, best clothe ever to pick up girls.');
        $phpTeeShirt->setRawDescription('A nice PHP tee-shirt, best clothe ever to pick up girls.');
        $phpTeeShirt->setPrice(25);
        $phpTeeShirt->setStock(10000);
        $phpTeeShirt->setVat(0);
        $phpTeeShirt->setEnabled(true);
        $manager->persist($phpTeeShirt);
        $this->setReference('php_teeshirt_goodie_product', $phpTeeShirt);

        $this->addMediaToProduct(__DIR__.'/../data/files/php_tee_shirt.png', 'PHP tee-shirt', 'PHP tee-shirt', $phpTeeShirt);
        $this->addProductToCategory($phpTeeShirt, $clothesCategory, $manager);
        $this->addProductDeliveries($phpTeeShirt, $manager);
        $this->addProductToCollection($phpTeeShirt, $phpCollection, $manager);

        // Training products
        $sonataTraining = new Training();
        $sonataTraining->setSku('SONATA_TRAINING');
        $sonataTraining->setName('Sonata training');
        $sonataTraining->setSlug('sonata-training');
        $sonataTraining->setDescription('A 2 days training to learn Sonata bundles.');
        $sonataTraining->setRawDescription('A 2 days training to learn Sonata bundles.');
        $sonataTraining->setLevel(Training::LEVEL_CONFIRMED);
        $sonataTraining->setInstructorName('Thomas Rabaix');
        $sonataTraining->setDuration('2 days');
        $sonataTraining->setPrice(1450.00);
        $sonataTraining->setStock(1500);
        $sonataTraining->setVat(0);
        $sonataTraining->setEnabled(true);
        $manager->persist($sonataTraining);
        $this->setReference('sonata_training_goodie_product', $sonataTraining);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Sonata logo', 'Sonata logo', $sonataTraining);
        $this->addProductToCategory($sonataTraining, $sonataCategory, $manager);
        $this->addProductDeliveries($sonataTraining, $manager);
        $this->addProductToCollection($sonataTraining, $phpCollection, $manager);

        $manager->flush();

        // package needs ProductId, @todo: clean doctrine relation
        $this->addPackageToProduct($sonataTraining, $manager);
        $this->addPackageToProduct($phpPlush, $manager);
        $this->addPackageToProduct($phpMug, $manager);
        $this->addPackageToProduct($phpTeeShirt, $manager);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 51;
    }

    /**
     * Create a ProductCategory and adds given Product to given Category.
     *
     * @param ProductInterface  $product
     * @param CategoryInterface $category
     * @param bool              $enabled
     * @param ObjectManager     $manager
     */
    protected function addProductToCategory(ProductInterface $product, CategoryInterface $category, ObjectManager $manager, $enabled = true)
    {
        $productCategory = new ProductCategory();

        $productCategory->setEnabled($enabled);
        $productCategory->setProduct($product);
        $productCategory->setCategory($category);

        $manager->persist($productCategory);
    }

    /**
     * Create a ProductCollection and adds given Product to given Collection.
     *
     * @param ProductInterface    $product
     * @param CollectionInterface $collection
     * @param bool                $enabled
     * @param ObjectManager       $manager
     */
    protected function addProductToCollection(ProductInterface $product, CollectionInterface $collection, ObjectManager $manager, $enabled = true)
    {
        $productCollection = new ProductCollection();

        $productCollection->setEnabled($enabled);
        $productCollection->setProduct($product);
        $productCollection->setCollection($collection);

        $manager->persist($productCollection);
    }

    /**
     * Create and add deliveries for a given Product.
     *
     * @param ProductInterface $product
     * @param ObjectManager    $manager
     */
    protected function addProductDeliveries(ProductInterface $product, ObjectManager $manager)
    {
        $delivery = new Delivery();
        $delivery->setCountryCode('FR');
        $delivery->setCode('free');
        $delivery->setEnabled(true);
        $delivery->setPerItem(0);
        $delivery->setProduct($product);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('GB');
        $delivery->setCode('free');
        $delivery->setEnabled(true);
        $delivery->setPerItem(0);
        $delivery->setProduct($product);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('FR');
        $delivery->setCode('chronopost');
        $delivery->setEnabled(true);
        $delivery->setPerItem(rand(15, 30));
        $delivery->setProduct($product);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('GB');
        $delivery->setCode('ups');
        $delivery->setEnabled(true);
        $delivery->setPerItem(rand(15, 30));
        $delivery->setProduct($product);
        $manager->persist($delivery);
    }

    /**
     * @param $mediaFilename
     * @param $name
     * @param $description
     * @param ProductInterface $product
     */
    protected function addMediaToProduct($mediaFilename, $name, $description, ProductInterface $product)
    {
        $mediaManager = $this->getMediaManager();

        $file = new \SplFileInfo($mediaFilename);

        $media = $mediaManager->create();
        $media->setBinaryContent($file);
        $media->setEnabled(true);
        $media->setName($name);
        $media->setDescription($description);

        $mediaManager->save($media, 'sonata_product', 'sonata.media.provider.image');

        $product->setImage($media);
    }

    /**
     * @param ProductInterface $product
     * @param ObjectManager    $manager
     */
    protected function addPackageToProduct(ProductInterface $product, ObjectManager $manager)
    {
        $package = new Package();

        $package->setProductId($product->getId());
        $package->setWidth(rand(1, 50));
        $package->setHeight(rand(1, 50));
        $package->setLength(rand(1, 50));
        $package->setWeight(rand(1, 50));
        $package->setEnabled(true);
        $package->setCreatedAt(new \DateTime());
        $package->setUpdatedAt(new \DateTime());

        $manager->persist($package);
    }

    /**
     * Returns the plush sub-Category.
     *
     * @return \Application\Sonata\ProductBundle\Entity\Category
     */
    protected function getPlushesCategory()
    {
        return $this->getReference('plushes_goodies_category');
    }

    /**
     * Returns the Sonata sub-Category.
     *
     * @return \Application\Sonata\ProductBundle\Entity\Category
     */
    protected function getSonataTrainingCategory()
    {
        return $this->getReference('sonata_trainings_category');
    }

    /**
     * Returns the mugs sub-Category.
     *
     * @return \Application\Sonata\ProductBundle\Entity\Category
     */
    protected function getMugCategory()
    {
        return $this->getReference('sonata_mugs_category');
    }

    /**
     * Returns the clothes sub-Category.
     *
     * @return \Application\Sonata\ProductBundle\Entity\Category
     */
    protected function getClothesCategory()
    {
        return $this->getReference('sonata_clothes_category');
    }

    /**
     * Returns the PHP collection
     *
     * @return CollectionInterface
     */
    protected function getPhpCollection()
    {
        return $this->getReference('php_collection');
    }

    /**
     * Returns the Sonata MediaManager.
     *
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


}