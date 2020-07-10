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
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;

/**
 * Class LoadCollectionData.
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class LoadCollectionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    public function __construct(CollectionManagerInterface $collectionManager)
    {
        $this->collectionManager = $collectionManager;
    }

    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        $productContext = $this->getReference('context_product_catalog');

        // PHP Fan collection
        $php = $this->collectionManager->create();
        $php->setName('PHP Fan');
        $php->setSlug('php-fan');
        $php->setDescription('Everything a PHP Fan needs.');
        $php->setEnabled(true);
        $php->setContext($productContext);
        $this->collectionManager->save($php);

        $this->setReference('php_collection', $php);

        // Travels collection
        $travel = $this->collectionManager->create();
        $travel->setName('Travels');
        $travel->setSlug('travels');
        $travel->setDescription('Every travels you want');
        $travel->setEnabled(true);
        $travel->setContext($productContext);
        $this->collectionManager->save($travel);

        $this->setReference('travel_collection', $travel);

        // Dummy collection
        $dummy = $this->collectionManager->create();
        $dummy->setName('Dummys');
        $dummy->setSlug('Dummys');
        $dummy->setDescription('Every dummys you want');
        $dummy->setEnabled(true);
        $dummy->setContext($productContext);
        $this->collectionManager->save($dummy);

        $this->setReference('dummy_collection', $dummy);

        $manager->flush();
    }
}
