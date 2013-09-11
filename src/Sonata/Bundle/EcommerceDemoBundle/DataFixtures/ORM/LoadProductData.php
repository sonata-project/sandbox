<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\EcommerceDemoBundle\DataFixtures\ORM;

use Application\Sonata\ProductBundle\Entity\Delivery;
use Application\Sonata\ProductBundle\Entity\Goodie;
use Application\Sonata\ProductBundle\Entity\ProductCategory;
use Application\Sonata\ProductBundle\Entity\Training;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\Component\Product\CategoryInterface;
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
        $plushesCategory = $this->getPlushesCategory();
        $sonataCategory  = $this->getSonataTrainingCategory();

        // Goodies products
        $phpPlush = new Goodie();
        $phpPlush->setSku('PHP_PLUSH');
        $phpPlush->setName('PHP plush');
        $phpPlush->setSlug('php-plush');
        $phpPlush->setDescription('A PHP "elephpant" to enjoy your desk.');
        $phpPlush->setPrice(29.99);
        $phpPlush->setStock(2000);
        $phpPlush->setVat(0);
        $phpPlush->setEnabled(true);
        $manager->persist($phpPlush);
        $this->setReference('php_plush_goodie_product', $phpPlush);

        $this->addProductToCategory($phpPlush, $plushesCategory, $manager);
        $this->addProductDeliveries($phpPlush, $manager);

        // Training products
        $sonataTraining = new Training();
        $sonataTraining->setSku('SONATA_TRAINING');
        $sonataTraining->setName('Sonata training');
        $sonataTraining->setSlug('sonata-training');
        $sonataTraining->setDescription('A 2 days training to learn Sonata bundles.');
        $sonataTraining->setPrice(1450.00);
        $sonataTraining->setStock(25);
        $sonataTraining->setVat(0);
        $sonataTraining->setEnabled(true);
        $manager->persist($sonataTraining);
        $this->setReference('sonata_training_goodie_product', $sonataTraining);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Sonata logo', 'Sonata logo', $sonataTraining, $manager);
        $this->addProductToCategory($sonataTraining, $sonataCategory, $manager);
        $this->addProductDeliveries($sonataTraining, $manager);

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
        $delivery->setPerItem(2);
        $delivery->setProduct($product);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('GB');
        $delivery->setCode('free');
        $delivery->setEnabled(true);
        $delivery->setPerItem(2);
        $delivery->setProduct($product);
        $manager->persist($delivery);
    }

    protected function addMediaToProduct($mediaFilename, $name, $description, ProductInterface $product, ObjectManager $manager)
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