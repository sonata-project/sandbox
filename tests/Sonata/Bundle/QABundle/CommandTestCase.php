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

namespace Tests\Sonata\Bundle\QABundle;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\Request;

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
    public function runCommand(KernelBrowser $client, $command, $exceptionOnExitCode = true)
    {
        $kernel = $client->getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $kernel->getContainer()->get('request_stack')->push(new Request());

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

        if ($exceptionOnExitCode && 0 !== $return) {
            throw new \RuntimeException(sprintf('Return code is not 0: %s', $output));
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getConsoleLocation(KernelBrowser $client)
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
        return [
            ['sonata.user.admin.user',                   'App\\Entity\\User\\User'],
            ['sonata.user.admin.group',                  'App\\Entity\\User\\Group'],
            ['sonata.page.admin.page',                   'App\\Entity\\Page\\Page'],
            ['sonata.page.admin.block',                  'App\\Entity\\Page\\Block'],
            ['sonata.page.admin.snapshot',               'App\\Entity\\Page\\Snapshot'],
            ['sonata.page.admin.site',                   'App\\Entity\\Page\\Site'],
            ['sonata.news.admin.post',                   'App\\Entity\\News\\Post'],
            ['sonata.news.admin.comment',                'App\\Entity\\News\\Comment'],
            ['sonata.classification.admin.category',     'App\\Entity\\Classification\\Category'],
            ['sonata.classification.admin.tag',          'App\\Entity\\Classification\\Tag'],
            ['sonata.classification.admin.collection',   'App\\Entity\\Classification\\Collection'],
            ['sonata.classification.admin.context',      'App\\Entity\\Classification\\Context'],
            ['sonata.media.admin.media',                 'App\\Entity\\Media\\Media'],
            ['sonata.media.admin.gallery',               'App\\Entity\\Media\\Gallery'],
            ['sonata.media.admin.gallery_has_media',     'App\\Entity\\Media\\GalleryHasMedia'],
            ['sonata.notification.admin.message',        'App\\Entity\\Notification\\Message'],
            ['sonata.demo.admin.car',                    'Sonata\\Bundle\\DemoBundle\\Entity\\Car'],
            ['sonata.demo.admin.engine',                 'Sonata\\Bundle\\DemoBundle\\Entity\\Engine'],
            ['sonata.customer.admin.customer',           'App\\Entity\\Commerce\\Customer'],
            ['sonata.customer.admin.address',            'App\\Entity\\Commerce\\Address'],
            ['sonata.invoice.admin.invoice',             'App\\Entity\\Commerce\\Invoice'],
            ['sonata.order.admin.order',                 'App\\Entity\\Commerce\\Order'],
            ['sonata.order.admin.order_element',         'App\\Entity\\Commerce\\OrderElement'],
            ['sonata.product.admin.product',             'App\\Entity\\Commerce\\Product'],
            ['sonata.product.admin.product.category',    'App\\Entity\\Commerce\\ProductCategory'],
            ['sonata.product.admin.product.collection',  'App\\Entity\\Commerce\\ProductCollection'],
            ['sonata.product.admin.delivery',            'App\\Entity\\Commerce\\Delivery'],
        ];
    }

    /**
     * Returns declared caches.
     *
     * @return array
     */
    public static function getCacheList()
    {
        return [
            ['sonata.page.cache.esi', '{}'],
            ['sonata.page.cache.ssi', '{}'],
            ['sonata.page.cache.js_sync', '{}'],
            ['sonata.page.cache.js_async', '{}'],
            ['sonata.cache.noop', '{}'],
        ];
    }

    /**
     * Returns declared blocks.
     *
     * @return array
     */
    public static function getBlockList()
    {
        return [
            ['sonata.page.block.container'],
            ['sonata.page.block.children_pages'],
            ['sonata.media.block.media'],
            ['sonata.media.block.feature_media'],
            ['sonata.media.block.gallery'],
            ['sonata.admin.block.admin_list'],
            // ['sonata.admin_doctrine_orm.block.audit'],
            ['sonata.formatter.block.formatter'],
            ['sonata.block.service.empty'],
            ['sonata.block.service.text'],
            ['sonata.block.service.rss'],
            ['sonata.block.service.menu'],
            ['sonata.timeline.block.timeline'],
            ['sonata.customer.block.recent_customers'],
            ['sonata.basket.block.nb_items'],
            ['sonata.news.block.recent_posts'],
            ['sonata.news.block.recent_comments'],
            ['sonata.demo.block.account'],
            ['sonata.basket.block.nb_items'],
            ['sonata.order.block.recent_orders'],
            ['sonata.product.block.recent_products'],
        ];
    }

    /**
     * Returns declared Media.
     *
     * @return array
     */
    public static function getMediaList()
    {
        return [
            ['sonata.media.provider.image'],
            ['sonata.media.provider.file'],
            ['sonata.media.provider.youtube'],
            ['sonata.media.provider.dailymotion'],
            ['sonata.media.provider.vimeo'],
        ];
    }

    /**
     * Returns declared consumer.
     *
     * @return array
     */
    public static function getConsumerList()
    {
        return [
            ['sonata.page.create_snapshots', 'sonata.page.notification.create_snapshots'],
            ['sonata.page.create_snapshot', 'sonata.page.notification.create_snapshot'],
            ['sonata.page.cleanup_snapshots', 'sonata.page.notification.cleanup_snapshots'],
            ['sonata.page.cleanup_snapshot', 'sonata.page.notification.cleanup_snapshot'],
            ['sonata.media.create_thumbnail', 'sonata.media.notification.create_thumbnail'],
            ['mailer', 'sonata.notification.consumer.swift_mailer'],
            ['logger', 'sonata.notification.consumer.logger'],
        ];
    }
}
