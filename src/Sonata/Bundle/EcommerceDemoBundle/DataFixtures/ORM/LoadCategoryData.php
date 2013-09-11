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

use Application\Sonata\ProductBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Category fixtures loader.
 *
 * @author Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Goodies category
        $goodies = new Category();
        $goodies->setName('Goodies');
        $goodies->setSlug('goodies');
        $goodies->setDescription('Some goodies related to Sonata and Symfony world.');
        $goodies->setEnabled(true);
        $manager->persist($goodies);
        $this->setReference('goodies_category', $goodies);

        // Training category
        $training = new Category();
        $training->setName('Trainings');
        $training->setSlug('trainings');
        $training->setDescription('Want to learn Sonata? Check out our trainings.');
        $training->setEnabled(true);
        $manager->persist($training);
        $this->setReference('trainings_category', $training);

        // Goodies sub-categories
        $plushes = new Category();
        $plushes->setParent($goodies);
        $plushes->setName('Plushes');
        $plushes->setSlug('plushes');
        $plushes->setDescription('Some plushes.');
        $plushes->setEnabled(true);
        $manager->persist($plushes);
        $this->setReference('plushes_goodies_category', $plushes);

        // Training sub-categories
        $symfony = new Category();
        $symfony->setParent($training);
        $symfony->setName('Symfony2');
        $symfony->setSlug('symfony2');
        $symfony->setDescription('Symfony2 trainings, with experts.');
        $symfony->setEnabled(true);
        $manager->persist($symfony);
        $this->setReference('symfony_trainings_category', $symfony);

        $sonata = new Category();
        $sonata->setParent($training);
        $sonata->setName('Sonata');
        $sonata->setSlug('sonata');
        $sonata->setDescription('Learn how to use Sonata.');
        $sonata->setEnabled(true);
        $manager->persist($sonata);
        $this->setReference('sonata_trainings_category', $sonata);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 50;
    }
}