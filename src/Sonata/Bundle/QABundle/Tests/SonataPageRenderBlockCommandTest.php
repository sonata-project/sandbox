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

class SonataPageRenderBlockCommandTest extends CommandTestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testMissingArguments()
    {
        $client = self::createClient();

        $this->runCommand($client, "sonata:page:render-block");
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDumpInvalidService()
    {
        $client = self::createClient();

        $this->runCommand($client, "sonata:page:render-block sonata.page.manager.page 1 1");
    }

    /**
     * @expectedException \Sonata\PageBundle\Exception\PageNotFoundException
     */
    public function testDumpInvalidPageId()
    {
        $client = self::createClient();

        $this->runCommand($client, "sonata:page:render-block sonata.page.cms.page 9999999 99999999");
    }

    public function testDump()
    {
        $client = self::createClient();

        $page = $client->getContainer()->get('sonata.page.manager.page')->findOneBy(array());
        $block = $client->getContainer()->get('sonata.page.manager.block')->findOneBy(array(
            'page' => $page->getId()
        ));

        $output = $this->runCommand($client, sprintf("sonata:page:render-block sonata.page.cms.page %d %d", $page->getId(), $block->getId()));

        $this->assertContains('BlockContext Information', $output);
        $this->assertContains('Response Output', $output);
        $this->assertContains("200\nOK", $output);
    }
}
