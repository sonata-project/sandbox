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
        $productPool = $this->getProductPool();

        // default media
        $defaultMedia = $this->getMediaManager()->create();
        $defaultMedia->setBinaryContent(new \SplFileInfo(__DIR__.'/../data/files/sonata_logo.png'));
        $defaultMedia->setEnabled(true);
        $defaultMedia->setName('sonata_product_default_media');
        $defaultMedia->setDescription('Default Product media');
        $this->getMediaManager()->save($defaultMedia, 'sonata_product_default', 'sonata.media.provider.image');

        /*
         * @todo
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
        $phpPlush->setVat(19.6);
        $phpPlush->setEnabled(true);
        $manager->persist($phpPlush);
        $this->setReference('php_plush_goodie_product', $phpPlush);

        $this->addMediaToProduct(__DIR__.'/../data/files/elephpant.png', 'PHP elePHPant', 'PHP elePHPant', $phpPlush);
        $this->addProductToCategory($phpPlush, $plushesCategory, $manager);
        $this->addProductDeliveries($phpPlush, $manager);
        $this->addProductToCollection($phpPlush, $phpCollection, $manager);
        $this->addPackageToProduct($phpPlush, $manager);

        $phpMug = new Goodie();
        $phpMug->setSku('PHP_MUG');
        $phpMug->setName('PHP mug');
        $phpMug->setSlug('php-mug');
        $phpMug->setDescription('You love coffee and PHP ? This mug is for you.');
        $phpMug->setRawDescription('You love coffee and PHP ? This mug is for you.');
        $phpMug->setPrice(9.99);
        $phpMug->setStock(10000);
        $phpMug->setVat(19.6);
        $phpMug->setEnabled(true);
        $manager->persist($phpMug);
        $this->setReference('php_mug_goodie_product', $phpMug);

        $this->addMediaToProduct(__DIR__.'/../data/files/php_mug.jpg', 'PHP mug', 'PHP mug', $phpMug);
        $this->addProductToCategory($phpMug, $mugCategory, $manager);
        $this->addProductDeliveries($phpMug, $manager);
        $this->addProductToCollection($phpMug, $phpCollection, $manager);
        $this->addPackageToProduct($phpMug, $manager);

        $phpTeeShirt = new Goodie();
        $phpTeeShirt->setSku('PHP_TSHIRT');
        $phpTeeShirt->setName('PHP tee-shirt');
        $phpTeeShirt->setSlug('php-t-shirt');
        $phpTeeShirt->setDescription('A nice PHP tee-shirt, best clothe ever to pick up girls.');
        $phpTeeShirt->setRawDescription('A nice PHP tee-shirt, best clothe ever to pick up girls.');
        $phpTeeShirt->setPrice(25);
        $phpTeeShirt->setStock(0);
        $phpTeeShirt->setVat(19.6);
        $phpTeeShirt->setEnabled(true);
        $manager->persist($phpTeeShirt);
        $this->setReference('php_teeshirt_goodie_product', $phpTeeShirt);

        $this->addMediaToProduct(__DIR__.'/../data/files/php_tee_shirt.png', 'PHP tee-shirt', 'PHP tee-shirt', $phpTeeShirt);
        $this->addProductToCategory($phpTeeShirt, $clothesCategory, $manager);
        $this->addProductDeliveries($phpTeeShirt, $manager);
        $this->addProductToCollection($phpTeeShirt, $phpCollection, $manager);
        $this->addPackageToProduct($phpTeeShirt, $manager);

        $maximumAir = new Goodie();
        $maximumAir->setSku('MAXIMUM_AIR_SONATA');
        $maximumAir->setName('Maximum Air Sonata Limited Edition');
        $maximumAir->setSlug('maximum-air-sonata-limited-edition');
        $maximumAir->setDescription('Get this limited edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story.');
        $maximumAir->setRawDescription('Get this limited edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story.');
        $maximumAir->setPrice(130);
        $maximumAir->setStock(500);
        $maximumAir->setVat(19.6);
        $maximumAir->setEnabled(false);
        $maximumAir->setImage($defaultMedia);
        $manager->persist($maximumAir);
        $this->setReference('maximum_air_sonata_product', $maximumAir);

        $this->addProductToCategory($maximumAir, $this->getReference('sonata_shoes_category'), $manager);
        $this->addProductDeliveries($maximumAir, $manager);
        $this->addProductToCollection($maximumAir, $phpCollection, $manager);
        $this->addPackageToProduct($maximumAir, $manager);

        $maximumAir = new Goodie();
        $maximumAir->setSku('MAXIMUM_AIR_SONATA_ULTIMATE');
        $maximumAir->setName('Maxmimum Air Sonata ULTIMATE Edition');
        $maximumAir->setSlug('maximum-air-sonata-ultimate-edition');
        $maximumAir->setDescription('Get this ULTIMATE edition of the MAXIMUM AI SONATA and fly over PHP bugs. True story. Even more powerful than the limited edition.');
        $maximumAir->setRawDescription('Get this ULTIMATE edition of the MAXIMUM AI SONATA and fly over PHP bugs. True story. Even more powerful than the limited edition.');
        $maximumAir->setPrice(250);
        $maximumAir->setStock(30);
        $maximumAir->setVat(19.6);
        $maximumAir->setEnabled(true);
        $maximumAir->setImage($defaultMedia);
        $manager->persist($maximumAir);
        $this->setReference('maximum_air_sonata_ultimate_product', $maximumAir);

        $this->addProductToCategory($maximumAir, $this->getReference('sonata_shoes_category'), $manager);
        $this->addProductDeliveries($maximumAir, $manager);
        $this->addProductToCollection($maximumAir, $phpCollection, $manager);
        $this->addPackageToProduct($maximumAir, $manager);

        // Training products
        $sonataTraining = new Training();
        $sonataTraining->setName('Sonata trainings');
        $sonataTraining->setDescription('A training to learn Sonata bundles.');
        $sonataTraining->setRawDescription('A training to learn Sonata bundles.');
        $sonataTraining->setVat(5.5);
        $sonataTraining->setEnabled(true);
        $manager->persist($sonataTraining);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Sonata logo', 'Sonata logo', $sonataTraining);
        $this->addProductToCategory($sonataTraining, $sonataCategory, $manager);
        $this->addProductDeliveries($sonataTraining, $manager);
        $this->addProductToCollection($sonataTraining, $phpCollection, $manager);
        $this->addPackageToProduct($sonataTraining, $manager);

        $trainingProvider = $productPool->getProvider($sonataTraining);

        // Training beginner variation
        $sonataTrainingBeginner = $this->generateDefaultTrainingBeginnerVariation($trainingProvider, $sonataTraining);
        $sonataTrainingBeginner->setStock(1500);
        $sonataTrainingBeginner->setEnabled(true);
        $manager->persist($sonataTrainingBeginner);
        $this->setReference('sonata_training_goodie_product_beginner', $sonataTrainingBeginner);

        // Training confirmed variation
        $sonataTrainingConfirmed = $this->generateDefaultTrainingBeginnerVariation($trainingProvider, $sonataTraining);
        $sonataTrainingConfirmed->setSku('SONATA_TRAINING_CONFIRMED');
        $sonataTrainingConfirmed->setLevel(Training::LEVEL_CONFIRMED);
        $sonataTrainingConfirmed->setPrice(1450.00);
        $sonataTrainingConfirmed->setStock(1500);
        $sonataTrainingConfirmed->setEnabled(true);
        $manager->persist($sonataTrainingConfirmed);
        $this->setReference('sonata_training_goodie_product_confirmed', $sonataTrainingConfirmed);

        // Training expert variation
        $sonataTrainingExpert = $this->generateDefaultTrainingBeginnerVariation($trainingProvider, $sonataTraining);
        $sonataTrainingExpert->setSku('SONATA_TRAINING_EXPERTS');
        $sonataTrainingExpert->setName('Sonata training for experts');
        $sonataTrainingExpert->setLevel(Training::LEVEL_EXPERT);
        $sonataTrainingExpert->setDuration('3 days');
        $sonataTrainingExpert->setPrice(2500.00);
        $sonataTrainingExpert->setStock(1500);
        $sonataTrainingExpert->setEnabled(true);
        $manager->persist($sonataTrainingExpert);
        $this->setReference('sonata_training_goodie_product_expert', $sonataTrainingExpert);

        // PHP disabled master products
        $phpDisabledTraining = new Training();
        $phpDisabledTraining->setName('PHP disabled training');
        $phpDisabledTraining->setDescription('A training to learn how to program using PHP.');
        $phpDisabledTraining->setRawDescription('A training to learn how to program using PHP.');
        $phpDisabledTraining->setVat(5.5);
        $phpDisabledTraining->setEnabled(false);
        $phpDisabledTraining->setStock(0);
        $manager->persist($phpDisabledTraining);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Sonata logo', 'Sonata logo', $phpDisabledTraining);
        $this->addProductToCategory($phpDisabledTraining, $sonataCategory, $manager);
        $this->addProductDeliveries($phpDisabledTraining, $manager);
        $this->addProductToCollection($phpDisabledTraining, $phpCollection, $manager);
        $this->addPackageToProduct($phpDisabledTraining, $manager);

        $trainingProvider = $productPool->getProvider($phpDisabledTraining);

        // PHP disabled master products
        $phpDisabledTrainingBeginner = $this->generateDefaultTrainingBeginnerVariation($trainingProvider, $phpDisabledTraining);
        $phpDisabledTrainingBeginner->setSku('PHP_TRAINING_BEGINNERS');
        $phpDisabledTrainingBeginner->setStock(1500);
        $phpDisabledTrainingBeginner->setEnabled(true);
        $manager->persist($phpDisabledTrainingBeginner);
        $this->setReference('php_training_product_beginner', $phpDisabledTrainingBeginner);

        // PHP disabled master products
        $phpWorkingTraining = new Training();
        $phpWorkingTraining->setName('PHP working training');
        $phpWorkingTraining->setDescription('A training to learn how to program using PHP.');
        $phpWorkingTraining->setRawDescription('A training to learn how to program using PHP.');
        $phpWorkingTraining->setVat(5.5);
        $phpWorkingTraining->setEnabled(true);
        $phpWorkingTraining->setStock(0);
        $manager->persist($phpWorkingTraining);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Sonata logo', 'Sonata logo', $phpWorkingTraining);
        $this->addProductToCategory($phpWorkingTraining, $sonataCategory, $manager);
        $this->addProductDeliveries($phpWorkingTraining, $manager);
        $this->addProductToCollection($phpWorkingTraining, $phpCollection, $manager);
        $this->addPackageToProduct($phpWorkingTraining, $manager);

        $trainingProvider = $productPool->getProvider($phpWorkingTraining);

        // PHP disabled master products
        $phpWorkingTrainingBeginner = $this->generateDefaultTrainingBeginnerVariation($trainingProvider, $phpWorkingTraining);
        $phpWorkingTrainingBeginner->setSku('PHP_WORKING_TRAINING_BEGINNERS');
        $phpWorkingTrainingBeginner->setStock(0);
        $phpWorkingTrainingBeginner->setEnabled(true);
        $manager->persist($phpWorkingTrainingBeginner);
        $this->setReference('php_working_training_product_beginner', $phpWorkingTrainingBeginner);

        // PHP disabled child
        $phpDisabledChild = new Training();
        $phpDisabledChild->setName('PHP disabled child training');
        $phpDisabledChild->setDescription('A training to learn how to program using PHP.');
        $phpDisabledChild->setRawDescription('A training to learn how to program using PHP.');
        $phpDisabledChild->setVat(5.5);
        $phpDisabledChild->setEnabled(true);
        $phpDisabledChild->setStock(0);
        $manager->persist($phpDisabledChild);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Sonata logo', 'Sonata logo', $phpDisabledChild);
        $this->addProductToCategory($phpDisabledChild, $sonataCategory, $manager);
        $this->addProductDeliveries($phpDisabledChild, $manager);
        $this->addProductToCollection($phpDisabledChild, $phpCollection, $manager);
        $this->addPackageToProduct($phpDisabledChild, $manager);

        $trainingProvider = $productPool->getProvider($phpDisabledChild);

        // PHP disabled master products
        $phpDisabledChildBeginner = $this->generateDefaultTrainingBeginnerVariation($trainingProvider, $phpDisabledChild);
        $phpDisabledChildBeginner->setSku('PHP_DISABLED_CHILD_TRAINING_BEGINNERS');
        $phpDisabledChildBeginner->setStock(100);
        $manager->persist($phpDisabledChildBeginner);
        $this->setReference('php_disabled_child_training_product_beginner', $phpDisabledChildBeginner);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 12;
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

        $product->addProductCategory($productCategory);

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

        $product->addProductCollection($productCollection);

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
        $product->addDelivery($delivery);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('GB');
        $delivery->setCode('free');
        $delivery->setEnabled(true);
        $delivery->setPerItem(0);
        $delivery->setProduct($product);
        $product->addDelivery($delivery);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('FR');
        $delivery->setCode('chronopost');
        $delivery->setEnabled(true);
        $delivery->setPerItem(rand(15, 30));
        $delivery->setProduct($product);
        $product->addDelivery($delivery);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('GB');
        $delivery->setCode('ups');
        $delivery->setEnabled(true);
        $delivery->setPerItem(rand(15, 30));
        $delivery->setProduct($product);
        $product->addDelivery($delivery);
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

        $package->setProduct($product);
        $package->setWidth(rand(1, 50));
        $package->setHeight(rand(1, 50));
        $package->setLength(rand(1, 50));
        $package->setWeight(rand(1, 50));
        $package->setEnabled(true);
        $package->setCreatedAt(new \DateTime());
        $package->setUpdatedAt(new \DateTime());

        $product->addPackage($package);

        $manager->persist($package);
    }

    /**
     * Returns the plush sub-Category.
     *
     * @return CategoryInterface
     */
    protected function getPlushesCategory()
    {
        return $this->getReference('plushes_goodies_category');
    }

    /**
     * Returns the Sonata sub-Category.
     *
     * @return CategoryInterface
     */
    protected function getSonataTrainingCategory()
    {
        return $this->getReference('sonata_trainings_category');
    }

    /**
     * Returns the mugs sub-Category.
     *
     * @return CategoryInterface
     */
    protected function getMugCategory()
    {
        return $this->getReference('sonata_mugs_category');
    }

    /**
     * Returns the clothes sub-Category.
     *
     * @return CategoryInterface
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
     * Return the Product Pool.
     *
     * @return \Sonata\Component\Product\Pool
     */
    protected function getProductPool()
    {
        return $this->container->get('sonata.product.pool');
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param \Sonata\Component\Product\ProductProviderInterface $provider
     * @param \Sonata\Component\Product\ProductInterface $parent
     *
     * @return Training
     */
    protected function generateDefaultTrainingBeginnerVariation($provider, $parent)
    {
        $entity = $provider->createVariation($parent);

        $entity->setName('PHP working training for beginners');
        $entity->setLevel(Training::LEVEL_BEGINNER);
        $entity->setInstructorName('Thomas Rabaix');
        $entity->setDuration('1 day');
        $entity->setPrice(450.00);
        $entity->setStock(100);
        $entity->setEnabled(false);

        return $entity;
    }
}
