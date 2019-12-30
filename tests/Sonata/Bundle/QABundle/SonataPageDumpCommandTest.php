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

namespace Sonata\Bundle\QABundle\Tests;

class SonataPageDumpCommandTest extends CommandTestCase
{
    public function testMissingArguments()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();

        $this->runCommand($client, 'sonata:page:dump-page');
    }

    public function testDumpInvalidService()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();

        $this->runCommand($client, 'sonata:page:dump-page sonata.page.manager.page 1');
    }

    public function testDumpInvalidPageId()
    {
        $this->expectException(\Sonata\PageBundle\Exception\PageNotFoundException::class);

        $client = self::createClient();

        $this->runCommand($client, 'sonata:page:dump-page sonata.page.cms.page 9999999');
    }

    public function testDump()
    {
        $client = self::createClient();

        $page = $client->getContainer()->get('sonata.page.manager.page')->findOneBy([]);

        $output = $this->runCommand($client, sprintf('sonata:page:dump-page sonata.page.cms.page %d', $page->getId()));

        $this->assertContains('Kind', $output);
        $this->assertContains('Blocks', $output);
    }
}
