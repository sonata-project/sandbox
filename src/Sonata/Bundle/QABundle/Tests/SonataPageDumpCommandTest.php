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

class SonataPageDumpCommandTest extends CommandTestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testMissingArguments()
    {
        $client = self::createClient();

        $this->runCommand($client, "sonata:page:dump-page");
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDumpInvalidService()
    {
        $client = self::createClient();

        $this->runCommand($client, "sonata:page:dump-page sonata.page.manager.page 1");
    }

    /**
     * @expectedException \Sonata\PageBundle\Exception\PageNotFoundException
     */
    public function testDumpInvalidPageId()
    {
        $client = self::createClient();

        $this->runCommand($client, "sonata:page:dump-page sonata.page.cms.page 9999999");
    }

    public function testDump()
    {
        $client = self::createClient();

        $page = $client->getContainer()->get('sonata.page.manager.page')->findOneBy(array());

        $output = $this->runCommand($client, sprintf("sonata:page:dump-page sonata.page.cms.page %d", $page->getId()));

        $this->assertContains('Kind', $output);
        $this->assertContains('Blocks', $output);
    }
}
