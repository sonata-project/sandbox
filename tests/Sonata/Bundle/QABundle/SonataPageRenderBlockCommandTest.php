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

class SonataPageRenderBlockCommandTest extends CommandTestCase
{
    public function testMissingArguments()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();

        $this->runCommand($client, 'sonata:page:render-block');
    }

    public function testDumpInvalidService()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();

        $this->runCommand($client, 'sonata:page:render-block sonata.page.manager.page 1 1');
    }

    public function testDumpInvalidPageId()
    {
        $this->expectException(\Sonata\PageBundle\Exception\PageNotFoundException::class);

        $client = self::createClient();

        $this->runCommand($client, 'sonata:page:render-block sonata.page.cms.page 9999999 99999999');
    }

    public function testDump()
    {
        $this->markTestSkipped('The "sonata.block.context_manager" service or alias has been removed or inlined when the container was compiled.');

        $client = self::createClient();

        $page = $client->getContainer()->get('sonata.page.manager.page')->findOneBy([]);
        $block = $client->getContainer()->get('sonata.page.manager.block')->findOneBy([
            'page' => $page->getId(),
        ]);

        $output = $this->runCommand($client, sprintf('sonata:page:render-block sonata.page.cms.page %d %d', $page->getId(), $block->getId()));

        $this->assertStringContainsString('BlockContext Information', $output);
        $this->assertStringContainsString('Response Output', $output);
        $this->assertStringContainsString("200\nOK", $output);
    }
}
