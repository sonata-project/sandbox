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
     * Runs a command and returns it output.
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
        return sprintf('%s/console', $client->getContainer()->getParameter('kernel.root_dir'));
    }

    /**
     * Return declared admin.
     *
     * @return array
     */
    public static function getAdminList()
    {
        return array(
            array('sonata.user.admin.user',                   'AppBundle\\Entity\\User\\User'),
            array('sonata.user.admin.group',                  'AppBundle\\Entity\\User\\Group'),
            array('sonata.page.admin.page',                   'AppBundle\\Entity\\Page\\Page'),
            array('sonata.page.admin.block',                  'AppBundle\\Entity\\Page\\Block'),
            array('sonata.page.admin.snapshot',               'AppBundle\\Entity\\Page\\Snapshot'),
            array('sonata.page.admin.site',                   'AppBundle\\Entity\\Page\\Site'),
            array('sonata.news.admin.post',                   'AppBundle\\Entity\\News\\Post'),
            array('sonata.news.admin.comment',                'AppBundle\\Entity\\News\\Comment'),
            array('sonata.classification.admin.category',     'AppBundle\\Entity\\Classification\\Category'),
            array('sonata.classification.admin.tag',          'AppBundle\\Entity\\Classification\\Tag'),
            array('sonata.classification.admin.collection',   'AppBundle\\Entity\\Classification\\Collection'),
            array('sonata.classification.admin.context',      'AppBundle\\Entity\\Classification\\Context'),
            array('sonata.media.admin.media',                 'AppBundle\\Entity\\Media\\Media'),
            array('sonata.media.admin.gallery',               'AppBundle\\Entity\\Media\\Gallery'),
            array('sonata.media.admin.gallery_has_media',     'AppBundle\\Entity\\Media\\GalleryHasMedia'),
            array('sonata.notification.admin.message',        'AppBundle\\Entity\\Notification\\Message'),
            array('sonata.demo.admin.car',                    'Sonata\\Bundle\\DemoBundle\\Entity\\Car'),
            array('sonata.demo.admin.engine',                 'Sonata\\Bundle\\DemoBundle\\Entity\\Engine'),
            array('sonata.customer.admin.customer',           'AppBundle\\Entity\\Commerce\\Customer'),
            array('sonata.customer.admin.address',            'AppBundle\\Entity\\Commerce\\Address'),
            array('sonata.invoice.admin.invoice',             'AppBundle\\Entity\\Commerce\\Invoice'),
            array('sonata.order.admin.order',                 'AppBundle\\Entity\\Commerce\\Order'),
            array('sonata.order.admin.order_element',         'AppBundle\\Entity\\Commerce\\OrderElement'),
            array('sonata.product.admin.product',             'AppBundle\\Entity\\Commerce\\Product'),
            array('sonata.product.admin.product.category',    'AppBundle\\Entity\\Commerce\\ProductCategory'),
            array('sonata.product.admin.product.collection',  'AppBundle\\Entity\\Commerce\\ProductCollection'),
            array('sonata.product.admin.delivery',            'AppBundle\\Entity\\Commerce\\Delivery'),
        );
    }

    /**
     * Returns declared caches.
     *
     * @return array
     */
    public static function getCacheList()
    {
        return array(
            array('sonata.page.cache.esi', '{}'),
            array('sonata.page.cache.ssi', '{}'),
            array('sonata.page.cache.js_sync', '{}'),
            array('sonata.page.cache.js_async', '{}'),
            array('sonata.cache.noop', '{}'),
        );
    }

    /**
     * Returns declared blocks.
     *
     * @return array
     */
    public static function getBlockList()
    {
        return array(
            array('sonata.page.block.container'),
            array('sonata.page.block.children_pages'),
            array('sonata.media.block.media'),
            array('sonata.media.block.feature_media'),
            array('sonata.media.block.gallery'),
            array('sonata.admin.block.admin_list'),
            array('sonata.admin_doctrine_orm.block.audit'),
            array('sonata.formatter.block.formatter'),
            array('sonata.block.service.empty'),
            array('sonata.block.service.text'),
            array('sonata.block.service.rss'),
            array('sonata.block.service.menu'),
            array('sonata.timeline.block.timeline'),
            array('sonata.customer.block.recent_customers'),
            array('sonata.basket.block.nb_items'),
            array('sonata.news.block.recent_posts'),
            array('sonata.news.block.recent_comments'),
            array('sonata.user.block.menu'),
            array('sonata.user.block.account'),
            array('sonata.basket.block.nb_items'),
            array('sonata.order.block.recent_orders'),
            array('sonata.product.block.recent_products'),
        );
    }

    /**
     * Returns declared Media.
     *
     * @return array
     */
    public static function getMediaList()
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
     * Returns declared consumer.
     *
     * @return array
     */
    public static function getConsumerList()
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
