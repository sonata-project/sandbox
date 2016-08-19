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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadCollectionData.
 *
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class LoadCollectionData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
     * Returns the Sonata CollectionManager.
     *
     * @return \Sonata\CoreBundle\Model\ManagerInterface
     */
    public function getCollectionManager()
    {
        return $this->container->get('sonata.classification.manager.collection');
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $productContext = $this->getReference('context_product_catalog');

        // PHP Fan collection
        $php = $this->getCollectionManager()->create();
        $php->setName('PHP Fan');
        $php->setSlug('php-fan');
        $php->setDescription('Everything a PHP Fan needs.');
        $php->setEnabled(true);
        $php->setContext($productContext);
        $this->getCollectionManager()->save($php);

        $this->setReference('php_collection', $php);

        // Travels collection
        $travel = $this->getCollectionManager()->create();
        $travel->setName('Travels');
        $travel->setSlug('travels');
        $travel->setDescription('Every travels you want');
        $travel->setEnabled(true);
        $travel->setContext($productContext);
        $this->getCollectionManager()->save($travel);

        $this->setReference('travel_collection', $travel);

        // Dummy collection
        $dummy = $this->getCollectionManager()->create();
        $dummy->setName('Dummys');
        $dummy->setSlug('Dummys');
        $dummy->setDescription('Every dummys you want');
        $dummy->setEnabled(true);
        $dummy->setContext($productContext);
        $this->getCollectionManager()->save($dummy);

        $this->setReference('dummy_collection', $dummy);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
