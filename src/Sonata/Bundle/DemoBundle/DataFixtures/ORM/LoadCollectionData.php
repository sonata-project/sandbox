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
use Sonata\ClassificationBundle\Entity\CollectionManager;
use Sonata\ClassificationBundle\Model\Collection;

/**
 * Class LoadCollectionData.
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class LoadCollectionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var CollectionManager
     */
    protected $collectionManager;

    public function __construct(CollectionManager $collectionManager)
    {
        $this->collectionManager = $collectionManager;
    }

    public function getOrder(): int
    {
        return 4;
    }

    public function load(ObjectManager $manager): void
    {
        $productContext = $this->getReference('context_product_catalog');

        // PHP Fan
        $php = $this->createCollection('PHP Fan', 'php-fan', 'Everything a PHP Fan needs.', true, $productContext);
        $this->setReference('php_collection', $php);

        // Travels collection
        $travel = $this->createCollection('Travels', 'travels', 'Every travels you want.', true, $productContext);
        $this->setReference('travel_collection', $travel);

        // Dummy collection
        $dummy = $this->createCollection('Dummys', 'dummys', 'Every dummys you want.', true, $productContext);
        $this->setReference('dummy_collection', $dummy);

        // Disabled collection for testing purpose
        $shoes = $this->createCollection('Shoes', '$shoes', 'Every shoes you want.', false, $productContext);
        $this->setReference('shoes_collection', $shoes);

        $this->collectionManager->getObjectManager()->flush();
    }

    protected function createCollection(string $name, string $slug, string $description, bool $enabled, $context): Collection
    {
        $collection = $this->collectionManager->create();
        $collection->setName($name);
        $collection->setSlug($slug);
        $collection->setDescription($description);
        $collection->setEnabled($enabled);
        $collection->setContext($context);
        $this->collectionManager->save($collection, false);

        return $collection;
    }
}
