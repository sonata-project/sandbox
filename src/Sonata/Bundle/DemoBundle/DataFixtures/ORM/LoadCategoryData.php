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
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Model\Category;
use Sonata\ClassificationBundle\Model\Context;

/**
 * Category fixtures loader.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function getOrder(): int
    {
        return 2;
    }

    public function load(ObjectManager $manager): void
    {
        // Default category
        $defaultCategory = $this->createRootCategory('Default Category', 'default', $this->getReference('context_default'));
        $this->setReference('root_default_category', $defaultCategory);

        // Root product category
        $rootProduct = $this->createRootCategory('Root Products', 'products', $this->getReference('context_product_catalog'));
        $this->setReference('root_products_category', $rootProduct);

        // Travels category
        $travels = $this->createCategory('Travels', 'travels', 'Discover our travels', true, $rootProduct);
        $this->setReference('travels_category', $travels);

        // Asia category
        $asia = $this->createCategory('Asia', 'asia', 'Want to travel in Asia? Check out our travels.', true, $travels);
        $this->setReference('travels_asia_category', $asia);

        // Japan category
        $japan = $this->createCategory('Japan', 'japan', 'Want to travel in Japan? Check out our travels.', true, $asia);
        $this->setReference('travels_japan_category', $japan);

        // North America category
        $northAmerica = $this->createCategory('North America', 'north-america', 'Want to travel in North America? Check out our travels.', true, $travels);
        $this->setReference('travels_north_america_category', $northAmerica);

        // Canada category
        $canada = $this->createCategory('Canada', 'canada', 'Want to travel in Canada? Check out our travels.', true, $northAmerica);
        $this->setReference('travels_canada_category', $canada);

        // Quebec category
        $quebec = $this->createCategory('Quebec', 'quebec', 'Want to travel in Quebec? Check out our travels.', true, $canada);
        $this->setReference('travels_quebec_category', $quebec);

        // Europe category
        $europe = $this->createCategory('Europe', 'europe', 'Want to travel in Europe? Check out our travels.', true, $travels);
        $this->setReference('travels_europe_category', $europe);

        // Switzerland category
        $switzerland = $this->createCategory('Switzerland', 'switzerland', 'Want to travel in Switzerland? Check out our travels.', true, $europe);
        $this->setReference('travels_switzerland_category', $switzerland);

        // France category
        $france = $this->createCategory('France', 'france', 'Want to travel in France? Check out our travels.', true, $europe);
        $this->setReference('travels_france_category', $france);

        // Paris category
        $paris = $this->createCategory('Paris', 'paris', 'Want to travel in Paris? Check out our travels.', true, $europe);
        $this->setReference('travels_paris_category', $paris);

        // Great britain category
        $greatBritain = $this->createCategory('Great Britain', 'great-britain', 'Want to travel in Great Britain? Check out our travels.', true, $travels);
        $this->setReference('travels_great_britain_category', $greatBritain);

        // London category
        $london = $this->createCategory('London', 'london', 'Want to travel in London? Check out our travels.', true, $travels);
        $this->setReference('travels_london_category', $london);

        // Goodies category
        $goodies = $this->createCategory('Goodies', 'goodies', 'Some goodies related to Sonata and Symfony world.', true, $rootProduct);
        $this->setReference('goodies_category', $goodies);

        // Goodies sub-categories
        $plushes = $this->createCategory('Plushes', 'plushes', 'Some plushes.', true, $goodies);
        $this->setReference('plushes_goodies_category', $plushes);

        // Mugs sub-categories
        $mugs = $this->createCategory('Mugs', 'mugs', 'Some mugs.', true, $goodies);
        $this->setReference('sonata_mugs_category', $mugs);

        // Clothing sub-categories
        $clothes = $this->createCategory('Clothes', 'clothes', 'Clothes for geeks.', true, $goodies);
        $this->setReference('sonata_clothes_category', $clothes);

        // Dummy category
        $dummy = $this->createCategory('Dummy', 'dummy', 'Dummy category with many dummy products.', true, $rootProduct);
        $this->setReference('dummy_category', $dummy);

        // Disabled category for testing purpose
        $shoes = $this->createCategory('Shoes', 'shoes', 'Get the last coolest Sonata shoes (seriously)', false, $goodies);
        $this->setReference('sonata_shoes_category', $shoes);

        $this->categoryManager->getObjectManager()->flush();
    }

    protected function createRootCategory(string $name, string $slug, Context $context): Category
    {
        $rootCategory = $this->categoryManager->create();
        $rootCategory->setName($name);
        $rootCategory->setSlug($slug);
        $rootCategory->setEnabled(true);
        $rootCategory->setContext($context);

        $this->categoryManager->save($rootCategory, false);

        return $rootCategory;
    }

    protected function createCategory(string $name, string $slug, string $description, bool $enabled, Category $parent): Category
    {
        $category = $this->categoryManager->create();
        $category->setParent($parent);
        $category->setName($name);
        $category->setSlug($slug);
        $category->setDescription($description);
        $category->setEnabled($enabled);

        $this->categoryManager->save($category, false);

        return $category;
    }
}
