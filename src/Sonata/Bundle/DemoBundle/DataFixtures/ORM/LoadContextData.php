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
class LoadContextData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
     * Returns the Sonata ContextaManager.
     *
     * @return \Sonata\CoreBundle\Model\ManagerInterface
     */
    public function getContextManager()
    {
        return $this->container->get('sonata.classification.manager.context');
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $contextManager = $this->getContextManager();

        $default = $contextManager->create();
        $default->setId('default');
        $default->setName('Default');
        $default->setEnabled(true);
        $contextManager->save($default);

        $this->setReference('context_default', $default);

        $productContext = $contextManager->create();
        $productContext->setId('product_catalog');
        $productContext->setName('Product Catalog');
        $productContext->setEnabled(true);

        $contextManager->save($productContext);

        $this->setReference('context_product_catalog', $productContext);

        $newsContext = $contextManager->create();
        $newsContext->setId('news');
        $newsContext->setName('News');
        $newsContext->setEnabled(true);
        $contextManager->save($newsContext);

        $this->setReference('context_news', $newsContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
