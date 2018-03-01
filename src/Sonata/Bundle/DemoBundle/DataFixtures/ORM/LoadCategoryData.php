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

use Application\Sonata\ProductBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Category fixtures loader.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns the Sonata MediaManager.
     *
     * @return \Sonata\CoreBundle\Model\ManagerInterface
     */
    public function getCategoryManager()
    {
        return $this->container->get('sonata.classification.manager.category');
    }


    /**
     * Returns the Sonata ContextManager.
     *
     * @return \Sonata\CoreBundle\Model\ManagerInterface
     */
    public function getContextManager()
    {
        return $this->container->get('sonata.classification.manager.context');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $context = $this->getContextManager()->find('default');

        // Travels category
        $travels = $this->getCategoryManager()->create();
        $travels->setName('Travels');
        $travels->setSlug('travels');
        $travels->setDescription('Discover our travels');
        $travels->setEnabled(true);
        $travels->setContext($context);
        $this->getCategoryManager()->save($travels);
        $this->setReference('travels_category', $travels);

        // Asia category
        $asia = $this->getCategoryManager()->create();
        $asia->setParent($travels);
        $asia->setName('Asia');
        $asia->setSlug('asia');
        $asia->setDescription('Want to travel in Asia? Check out our travels.');
        $asia->setEnabled(true);
        $asia->setContext($context);
        $this->getCategoryManager()->save($asia);
        $this->setReference('travels_asia_category', $asia);

        // Japan category
        $japan = $this->getCategoryManager()->create();
        $japan->setParent($asia);
        $japan->setName('Japan');
        $japan->setSlug('japan');
        $japan->setDescription('Want to travel in Japan? Check out our travels.');
        $japan->setEnabled(true);
        $japan->setContext($context);
        $this->getCategoryManager()->save($japan);
        $this->setReference('travels_japan_category', $japan);

        // North America category
        $northAmerica = $this->getCategoryManager()->create();
        $northAmerica->setParent($travels);
        $northAmerica->setName('North America');
        $northAmerica->setSlug('north-america');
        $northAmerica->setDescription('Want to travel in North America? Check out our travels.');
        $northAmerica->setEnabled(true);
        $northAmerica->setContext($context);
        $this->getCategoryManager()->save($northAmerica);
        $this->setReference('travels_north_america_category', $northAmerica);

        // Canada category
        $canada = $this->getCategoryManager()->create();
        $canada->setParent($northAmerica);
        $canada->setName('Canada');
        $canada->setSlug('canada');
        $canada->setDescription('Want to travel in Canada? Check out our travels.');
        $canada->setEnabled(true);
        $canada->setContext($context);
        $this->getCategoryManager()->save($canada);
        $this->setReference('travels_canada_category', $canada);

        // Quebec category
        $quebec = $this->getCategoryManager()->create();
        $quebec->setParent($canada);
        $quebec->setName('Quebec');
        $quebec->setSlug('quebec');
        $quebec->setDescription('Want to travel in Quebec? Check out our travels.');
        $quebec->setEnabled(true);
        $quebec->setContext($context);
        $this->getCategoryManager()->save($quebec);
        $this->setReference('travels_quebec_category', $quebec);

        // Europe category
        $europe = $this->getCategoryManager()->create();
        $europe->setParent($travels);
        $europe->setName('Europe');
        $europe->setSlug('europe');
        $europe->setDescription('Want to travel in Europe? Check out our travels.');
        $europe->setEnabled(true);
        $europe->setContext($context);
        $this->getCategoryManager()->save($europe);
        $this->setReference('travels_europe_category', $europe);

        // Switzerland category
        $switzerland = $this->getCategoryManager()->create();
        $switzerland->setParent($europe);
        $switzerland->setName('Switzerland');
        $switzerland->setSlug('switzerland');
        $switzerland->setContext($context);
        $switzerland->setDescription('Want to travel in Switzerland? Check out our travels.');
        $switzerland->setEnabled(true);
        $this->getCategoryManager()->save($switzerland);
        $this->setReference('travels_switzerland_category', $switzerland);

        // France category
        $france = $this->getCategoryManager()->create();
        $france->setParent($europe);
        $france->setName('France');
        $france->setSlug('france');
        $france->setDescription('Want to travel in France? Check out our travels.');
        $france->setEnabled(true);
        $france->setContext($context);
        $this->getCategoryManager()->save($france);
        $this->setReference('travels_france_category', $france);

        // Paris category
        $paris = $this->getCategoryManager()->create();
        $paris->setParent($france);
        $paris->setName('Paris');
        $paris->setSlug('paris');
        $paris->setDescription('Want to travel in Paris? Check out our travels.');
        $paris->setEnabled(true);
        $paris->setContext($context);
        $this->getCategoryManager()->save($paris);
        $this->setReference('travels_paris_category', $paris);

        // Great britain category
        $greatBritain = $this->getCategoryManager()->create();
        $greatBritain->setParent($travels);
        $greatBritain->setName('Great Britain');
        $greatBritain->setSlug('great-britain');
        $greatBritain->setContext($context);
        $greatBritain->setDescription('Want to travel in Great Britain? Check out our travels.');
        $greatBritain->setEnabled(true);
        $this->getCategoryManager()->save($greatBritain);
        $this->setReference('travels_great_britain_category', $greatBritain);

        // London category
        $london = $this->getCategoryManager()->create();
        $london->setParent($travels);
        $london->setName('London');
        $london->setSlug('london');
        $london->setDescription('Want to travel in London? Check out our travels.');
        $london->setEnabled(true);
        $london->setContext($context);
        $this->getCategoryManager()->save($london);
        $this->setReference('travels_london_category', $london);

        // Goodies category
        $goodies = $this->getCategoryManager()->create();
        $goodies->setName('Goodies');
        $goodies->setSlug('goodies');
        $goodies->setDescription('Some goodies related to Sonata and Symfony world.');
        $goodies->setEnabled(true);
        $goodies->setContext($context);
        $this->getCategoryManager()->save($goodies);
        $this->setReference('goodies_category', $goodies);

        // Goodies sub-categories
        $plushes = $this->getCategoryManager()->create();
        $plushes->setParent($goodies);
        $plushes->setName('Plushes');
        $plushes->setSlug('plushes');
        $plushes->setDescription('Some plushes.');
        $plushes->setContext($context);
        $plushes->setEnabled(true);
        $this->getCategoryManager()->save($plushes);
        $this->setReference('plushes_goodies_category', $plushes);

        // Mugs sub-categories
        $mugs = $this->getCategoryManager()->create();
        $mugs->setParent($goodies);
        $mugs->setName('Mugs');
        $mugs->setSlug('mugs');
        $mugs->setContext($context);
        $mugs->setDescription('Some mugs.');
        $mugs->setEnabled(true);
        $this->getCategoryManager()->save($mugs);
        $this->setReference('sonata_mugs_category', $mugs);

        // Clothing sub-categories
        $clothes = $this->getCategoryManager()->create();
        $clothes->setParent($goodies);
        $clothes->setName('Clothes');
        $clothes->setSlug('clothes');
        $clothes->setDescription('Clothes for geeks.');
        $clothes->setEnabled(true);
        $clothes->setContext($context);
        $this->getCategoryManager()->save($clothes);
        $this->setReference('sonata_clothes_category', $clothes);

        // Dummy category
        $dummy = $this->getCategoryManager()->create();
        $dummy->setName('Dummy');
        $dummy->setSlug('dummy');
        $dummy->setDescription('Dummy category with many dummy products');
        $dummy->setEnabled(true);
        $dummy->setContext($context);
        $this->getCategoryManager()->save($dummy);

        $this->setReference('dummy_category', $dummy);

        // Disabled category for testing purpose
        $shoes = $this->getCategoryManager()->create();
        $shoes->setParent($goodies);
        $shoes->setName('Shoes');
        $shoes->setSlug('shoes');
        $shoes->setDescription('Get the last coolest Sonata shoes (seriously)');
        $shoes->setEnabled(true);
        $shoes->setContext($context);
        $this->getCategoryManager()->save($shoes);
        $this->setReference('sonata_shoes_category', $shoes);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 11;
    }
}
