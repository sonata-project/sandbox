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

        if ($exceptionOnExitCode && $return !== 0) {
            throw new \RuntimeException('Return code is not 0');
        }

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output .= fread($fp, 4096);
        }
        fclose($fp);

        return $output;
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
            array('sonata.news.admin.category',               'Application\\Sonata\\NewsBundle\\Entity\\Category'),
            array('sonata.news.admin.tag',                    'Application\\Sonata\\NewsBundle\\Entity\\Tag'),
            array('sonata.media.admin.media',                 'Application\\Sonata\\MediaBundle\\Entity\\Media'),
            array('sonata.media.admin.gallery',               'Application\\Sonata\\MediaBundle\\Entity\\Gallery'),
            array('sonata.media.admin.gallery_has_media',     'Application\\Sonata\\MediaBundle\\Entity\\GalleryHasMedia'),
            array('sonata.notification.admin.message',        'Application\\Sonata\\NotificationBundle\\Entity\\Message'),
            array('sonata.demo.admin.car',                    'Sonata\\Bundle\\DemoBundle\\Entity\\Car'),
            array('sonata.demo.admin.engine',                 'Sonata\\Bundle\\DemoBundle\\Entity\\Engine'),
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
        );
    }
}