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
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

/**
 * Class LoadCollectionData.
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class LoadContextData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ContextManagerInterface
     */
    protected $contextManager;

    public function __construct(ContextManagerInterface $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $default = $this->contextManager->create();
        $default->setId('default');
        $default->setName('Default');
        $default->setEnabled(true);
        $this->contextManager->save($default);

        $this->setReference('context_default', $default);

        $productContext = $this->contextManager->create();
        $productContext->setId('product_catalog');
        $productContext->setName('Product Catalog');
        $productContext->setEnabled(true);

        $this->contextManager->save($productContext);

        $this->setReference('context_product_catalog', $productContext);

        $newsContext = $this->contextManager->create();
        $newsContext->setId('news');
        $newsContext->setName('News');
        $newsContext->setEnabled(true);
        $this->contextManager->save($newsContext);

        $this->setReference('context_news', $newsContext);
    }
}
