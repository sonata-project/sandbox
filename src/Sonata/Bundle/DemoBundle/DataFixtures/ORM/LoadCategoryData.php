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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;

/**
 * Category fixtures loader.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var CategoryManagerInterface
     */
    protected $categoryManager;

    public function __construct(CategoryManagerInterface $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $defaultContext = $this->getReference('context_default');

        $defaultCategory = $this->categoryManager->create();
        $defaultCategory->setName('Default Category');
        $defaultCategory->setSlug('products');
        $defaultCategory->setEnabled(true);
        $defaultCategory->setContext($defaultContext);

        $this->setReference('root_default_category', $defaultCategory);

        $this->categoryManager->save($defaultCategory);

        $productContext = $this->getReference('context_product_catalog');

        $rootProduct = $this->categoryManager->create();
        $rootProduct->setName('Root Products');
        $rootProduct->setSlug('products');
        $rootProduct->setEnabled(true);
        $rootProduct->setContext($productContext);
        $this->setReference('products_category', $rootProduct);

        $this->setReference('root_products_category', $rootProduct);

        $this->categoryManager->save($rootProduct);

        // Travels category
        $travels = $this->categoryManager->create();
        $travels->setName('Travels');
        $travels->setSlug('travels');
        $travels->setParent($rootProduct);
        $travels->setDescription('Discover our travels');
        $travels->setEnabled(true);
        $this->categoryManager->save($travels);
        $this->setReference('travels_category', $travels);

        // Asia category
        $asia = $this->categoryManager->create();
        $asia->setParent($travels);
        $asia->setName('Asia');
        $asia->setSlug('asia');
        $asia->setDescription('Want to travel in Asia? Check out our travels.');
        $asia->setEnabled(true);
        $this->categoryManager->save($asia);
        $this->setReference('travels_asia_category', $asia);

        // Japan category
        $japan = $this->categoryManager->create();
        $japan->setParent($asia);
        $japan->setName('Japan');
        $japan->setSlug('japan');
        $japan->setDescription('Want to travel in Japan? Check out our travels.');
        $japan->setEnabled(true);
        $this->categoryManager->save($japan);
        $this->setReference('travels_japan_category', $japan);

        // North America category
        $northAmerica = $this->categoryManager->create();
        $northAmerica->setParent($travels);
        $northAmerica->setName('North America');
        $northAmerica->setSlug('north-america');
        $northAmerica->setDescription('Want to travel in North America? Check out our travels.');
        $northAmerica->setEnabled(true);
        $this->categoryManager->save($northAmerica);
        $this->setReference('travels_north_america_category', $northAmerica);

        // Canada category
        $canada = $this->categoryManager->create();
        $canada->setParent($northAmerica);
        $canada->setName('Canada');
        $canada->setSlug('canada');
        $canada->setDescription('Want to travel in Canada? Check out our travels.');
        $canada->setEnabled(true);
        $this->categoryManager->save($canada);
        $this->setReference('travels_canada_category', $canada);

        // Quebec category
        $quebec = $this->categoryManager->create();
        $quebec->setParent($canada);
        $quebec->setName('Quebec');
        $quebec->setSlug('quebec');
        $quebec->setDescription('Want to travel in Quebec? Check out our travels.');
        $quebec->setEnabled(true);
        $this->categoryManager->save($quebec);
        $this->setReference('travels_quebec_category', $quebec);

        // Europe category
        $europe = $this->categoryManager->create();
        $europe->setParent($travels);
        $europe->setName('Europe');
        $europe->setSlug('europe');
        $europe->setDescription('Want to travel in Europe? Check out our travels.');
        $europe->setEnabled(true);
        $this->categoryManager->save($europe);
        $this->setReference('travels_europe_category', $europe);

        // Switzerland category
        $switzerland = $this->categoryManager->create();
        $switzerland->setParent($europe);
        $switzerland->setName('Switzerland');
        $switzerland->setSlug('switzerland');
        $switzerland->setDescription('Want to travel in Switzerland? Check out our travels.');
        $switzerland->setEnabled(true);
        $this->categoryManager->save($switzerland);
        $this->setReference('travels_switzerland_category', $switzerland);

        // France category
        $france = $this->categoryManager->create();
        $france->setParent($europe);
        $france->setName('France');
        $france->setSlug('france');
        $france->setDescription('Want to travel in France? Check out our travels.');
        $france->setEnabled(true);
        $this->categoryManager->save($france);
        $this->setReference('travels_france_category', $france);

        // Paris category
        $paris = $this->categoryManager->create();
        $paris->setParent($france);
        $paris->setName('Paris');
        $paris->setSlug('paris');
        $paris->setDescription('Want to travel in Paris? Check out our travels.');
        $paris->setEnabled(true);
        $this->categoryManager->save($paris);
        $this->setReference('travels_paris_category', $paris);

        // Great britain category
        $greatBritain = $this->categoryManager->create();
        $greatBritain->setParent($travels);
        $greatBritain->setName('Great Britain');
        $greatBritain->setSlug('great-britain');
        $greatBritain->setDescription('Want to travel in Great Britain? Check out our travels.');
        $greatBritain->setEnabled(true);
        $this->categoryManager->save($greatBritain);
        $this->setReference('travels_great_britain_category', $greatBritain);

        // London category
        $london = $this->categoryManager->create();
        $london->setParent($travels);
        $london->setName('London');
        $london->setSlug('london');
        $london->setDescription('Want to travel in London? Check out our travels.');
        $london->setEnabled(true);
        $this->categoryManager->save($london);
        $this->setReference('travels_london_category', $london);

        // Goodies category
        $goodies = $this->categoryManager->create();
        $goodies->setName('Goodies');
        $goodies->setSlug('goodies');
        $goodies->setDescription('Some goodies related to Sonata and Symfony world.');
        $goodies->setEnabled(true);
        $goodies->setParent($rootProduct);
        $this->categoryManager->save($goodies);
        $this->setReference('goodies_category', $goodies);

        // Goodies sub-categories
        $plushes = $this->categoryManager->create();
        $plushes->setParent($goodies);
        $plushes->setName('Plushes');
        $plushes->setSlug('plushes');
        $plushes->setDescription('Some plushes.');
        $plushes->setEnabled(true);
        $this->categoryManager->save($plushes);
        $this->setReference('plushes_goodies_category', $plushes);

        // Mugs sub-categories
        $mugs = $this->categoryManager->create();
        $mugs->setParent($goodies);
        $mugs->setName('Mugs');
        $mugs->setSlug('mugs');
        $mugs->setDescription('Some mugs.');
        $mugs->setEnabled(true);
        $this->categoryManager->save($mugs);
        $this->setReference('sonata_mugs_category', $mugs);

        // Clothing sub-categories
        $clothes = $this->categoryManager->create();
        $clothes->setParent($goodies);
        $clothes->setName('Clothes');
        $clothes->setSlug('clothes');
        $clothes->setDescription('Clothes for geeks.');
        $clothes->setEnabled(true);
        $this->categoryManager->save($clothes);
        $this->setReference('sonata_clothes_category', $clothes);

        // Dummy category
        $dummy = $this->categoryManager->create();
        $dummy->setName('Dummy');
        $dummy->setSlug('dummy');
        $dummy->setDescription('Dummy category with many dummy products');
        $dummy->setEnabled(true);
        $dummy->setParent($rootProduct);
        $this->categoryManager->save($dummy);

        $this->setReference('dummy_category', $dummy);

        // Disabled category for testing purpose
        $shoes = $this->categoryManager->create();
        $shoes->setParent($goodies);
        $shoes->setName('Shoes');
        $shoes->setSlug('shoes');
        $shoes->setDescription('Get the last coolest Sonata shoes (seriously)');
        $shoes->setEnabled(true);
        $this->categoryManager->save($shoes);
        $this->setReference('sonata_shoes_category', $shoes);

        $manager->flush();
    }
}
