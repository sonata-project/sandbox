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
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Model\Context;

/**
 * Class LoadCollectionData.
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class LoadContextData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ContextManager
     */
    protected $contextManager;

    public function __construct(ContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    public function getOrder(): int
    {
        return 1;
    }

    public function load(ObjectManager $manager): void
    {
        $default = $this->createContext('default', 'Default');
        $this->setReference('context_default', $default);

        $productContext = $this->createContext('product_catalog', 'Product Catalog');
        $this->setReference('context_product_catalog', $productContext);

        $newsContext = $this->createContext('news', 'News');
        $this->setReference('context_news', $newsContext);

        $this->contextManager->getObjectManager()->flush();
    }

    protected function createContext(string $id, string $name): Context
    {
        $context = $this->contextManager->create();
        $context->setId($id);
        $context->setName($name);
        $context->setEnabled(true);

        $this->contextManager->save($context, false);

        return $context;
    }
}
