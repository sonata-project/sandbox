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
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Goodies category
        $goodies = $this->getCategoryManager()->create();
        $goodies->setName('Goodies');
        $goodies->setSlug('goodies');
        $goodies->setDescription('Some goodies related to Sonata and Symfony world.');
        $goodies->setEnabled(true);
        $this->getCategoryManager()->save($goodies);

        $this->setReference('goodies_category', $goodies);

        // Training category
        $training = $this->getCategoryManager()->create();
        $training->setName('Trainings');
        $training->setSlug('trainings');
        $training->setDescription('Want to learn Sonata? Check out our trainings.');
        $training->setEnabled(true);
        $this->getCategoryManager()->save($training);
        $this->setReference('trainings_category', $training);

        // Goodies sub-categories
        $plushes = $this->getCategoryManager()->create();
        $plushes->setParent($goodies);
        $plushes->setName('Plushes');
        $plushes->setSlug('plushes');
        $plushes->setDescription('Some plushes.');
        $plushes->setEnabled(true);
        $this->getCategoryManager()->save($plushes);
        $this->setReference('plushes_goodies_category', $plushes);

        // Mugs sub-categories
        $mugs = $this->getCategoryManager()->create();
        $mugs->setParent($goodies);
        $mugs->setName('Mugs');
        $mugs->setSlug('mugs');
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
        $this->getCategoryManager()->save($clothes);
        $this->setReference('sonata_clothes_category', $clothes);

        // Training sub-categories
        $symfony = $this->getCategoryManager()->create();
        $symfony->setParent($training);
        $symfony->setName('Symfony2');
        $symfony->setSlug('symfony2');
        $symfony->setDescription('Symfony2 trainings, with experts.');
        $symfony->setEnabled(true);
        $this->getCategoryManager()->save($symfony);
        $this->setReference('symfony_trainings_category', $symfony);

        // Sonata sub-category
        $sonata = $this->getCategoryManager()->create();
        $sonata->setParent($training);
        $sonata->setName('Sonata trainings');
        $sonata->setSlug('sonata-trainings');
        $sonata->setDescription('Learn how to use Sonata.');
        $sonata->setEnabled(true);
        $this->getCategoryManager()->save($sonata);
        $this->setReference('sonata_trainings_category', $sonata);

        // Disabled category for testing purpose
        $shoes = $this->getCategoryManager()->create();
        $shoes->setParent($goodies);
        $shoes->setName('Shoes');
        $shoes->setSlug('shoes');
        $shoes->setDescription('Get the last coolest Sonata shoes (seriously)');
        $shoes->setEnabled(false);
        $this->getCategoryManager()->save($shoes);
        $this->setReference('sonata_shoes_category', $sonata);

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
