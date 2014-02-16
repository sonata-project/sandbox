<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\QABundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Base class for testing the CLI tools.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class CommandTestCase extends WebTestCase
{
    /**
     * Runs a command and returns it output
     */
    public function runCommand(Client $client, $command, $exceptionOnExitCode = true)
    {
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $input = new StringInput($command);
        $output = new StreamOutput($fp = tmpfile());

        $application->setCatchExceptions(false);
        $return = $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output .= fread($fp, 4096);
        }
        fclose($fp);

        if ($exceptionOnExitCode && $return !== 0) {
            throw new \RuntimeException(sprintf('Return code is not 0: %s', $output));
        }

        return $output;
    }

    /**
     * @param Client $client
     *
     * @return string
     */
    public function getConsoleLocation(Client $client)
    {
        return sprintf("%s/console", $client->getContainer()->getParameter('kernel.root_dir'));
    }

    /**
     * Return declared admin
     *
     * @return array
     */
    static public function getAdminList()
    {
        return array(
            array('sonata.user.admin.user',                   'Application\\Sonata\\UserBundle\\Entity\\User'),
            array('sonata.user.admin.group',                  'Application\\Sonata\\UserBundle\\Entity\\Group'),
            array('sonata.page.admin.page',                   'Application\\Sonata\\PageBundle\\Entity\\Page'),
            array('sonata.page.admin.block',                  'Application\\Sonata\\PageBundle\\Entity\\Block'),
            array('sonata.page.admin.snapshot',               'Application\\Sonata\\PageBundle\\Entity\\Snapshot'),
            array('sonata.page.admin.site',                   'Application\\Sonata\\PageBundle\\Entity\\Site'),
            array('sonata.news.admin.post',                   'Application\\Sonata\\NewsBundle\\Entity\\Post'),
            array('sonata.news.admin.comment',                'Application\\Sonata\\NewsBundle\\Entity\\Comment'),
            array('sonata.classification.admin.category',     'Application\\Sonata\\ClassificationBundle\\Entity\\Category'),
            array('sonata.classification.admin.tag',          'Application\\Sonata\\ClassificationBundle\\Entity\\Tag'),
            array('sonata.classification.admin.collection',   'Application\\Sonata\\ClassificationBundle\\Entity\\Collection'),
            array('sonata.media.admin.media',                 'Application\\Sonata\\MediaBundle\\Entity\\Media'),
            array('sonata.media.admin.gallery',               'Application\\Sonata\\MediaBundle\\Entity\\Gallery'),
            array('sonata.media.admin.gallery_has_media',     'Application\\Sonata\\MediaBundle\\Entity\\GalleryHasMedia'),
            array('sonata.notification.admin.message',        'Application\\Sonata\\NotificationBundle\\Entity\\Message'),
            array('sonata.demo.admin.car',                    'Sonata\\Bundle\\DemoBundle\\Entity\\Car'),
            array('sonata.demo.admin.engine',                 'Sonata\\Bundle\\DemoBundle\\Entity\\Engine'),
            array('sonata.customer.admin.customer',           'Application\Sonata\CustomerBundle\Entity\Customer'),
            array('sonata.customer.admin.address',            'Application\Sonata\CustomerBundle\Entity\Address'),
            array('sonata.invoice.admin.invoice',             'Application\Sonata\InvoiceBundle\Entity\Invoice'),
            array('sonata.order.admin.order',                 'Application\Sonata\OrderBundle\Entity\Order'),
            array('sonata.order.admin.order_element',         'Application\Sonata\OrderBundle\Entity\OrderElement'),
            array('sonata.product.admin.product',             'Application\Sonata\ProductBundle\Entity\Product'),
            array('sonata.product.admin.product_category',    'Application\Sonata\ProductBundle\Entity\ProductCategory'),
            array('sonata.product.admin.product_collection',  'Application\Sonata\ProductBundle\Entity\ProductCollection'),
            array('sonata.product.admin.delivery',            'Application\Sonata\ProductBundle\Entity\Delivery'),
        );
    }

    /**
     * Returns declared caches
     *
     * @return array
     */
    static public function getCacheList()
    {
        return array(
            array('sonata.page.cache.esi', '{}' ),
            array('sonata.page.cache.ssi', '{}'),
            array('sonata.page.cache.js_sync', '{}'),
            array('sonata.page.cache.js_async', '{}'),
            array('sonata.cache.noop', '{}'),
        );
    }

    /**
     * Returns declared blocks
     *
     * @return array
     */
    static public function getBlockList()
    {
        return array(
            array('sonata.page.block.container', ),
            array('sonata.page.block.children_pages', ),
            array('sonata.media.block.media', ),
            array('sonata.media.block.feature_media', ),
            array('sonata.media.block.gallery', ),
            array('sonata.admin.block.admin_list', ),
            array('sonata.admin_doctrine_orm.block.audit', ),
            array('sonata.formatter.block.formatter', ),
            array('sonata.block.service.empty', ),
            array('sonata.block.service.text', ),
            array('sonata.block.service.rss', ),
            array('sonata.block.service.menu', ),
            array('sonata.timeline.block.timeline', ),
            array('sonata.customer.block.recent_customers', ),
            array('sonata.basket.block.nb_items', ),
            array('sonata.news.block.recent_posts', ),
            array('sonata.news.block.recent_comments', ),
            array('sonata.user.block.menu', ),
            array('sonata.user.block.account', ),
            array('sonata.basket.block.nb_items', ),
            array('sonata.order.block.recent_orders', ),
            array('sonata.product.block.recent_products', ),
        );
    }

    /**
     * Returns declared Media
     *
     * @return array
     */
    static public function getMediaList()
    {
        return array(
            array('sonata.media.provider.image'),
            array('sonata.media.provider.file'),
            array('sonata.media.provider.youtube'),
            array('sonata.media.provider.dailymotion'),
            array('sonata.media.provider.vimeo'),
        );
    }

    /**
     * Returns declared consumer
     *
     * @return array
     */
    static public function getConsumerList()
    {
        return array(
            array('sonata.page.create_snapshots', 'sonata.page.notification.create_snapshots'),
            array('sonata.page.create_snapshot', 'sonata.page.notification.create_snapshot'),
            array('sonata.page.cleanup_snapshots', 'sonata.page.notification.cleanup_snapshots'),
            array('sonata.page.cleanup_snapshot', 'sonata.page.notification.cleanup_snapshot'),
            array('sonata.media.create_thumbnail', 'sonata.media.notification.create_thumbnail'),
            array('mailer', 'sonata.notification.consumer.swift_mailer'),
            array('logger', 'sonata.notification.consumer.logger'),
        );
    }
}