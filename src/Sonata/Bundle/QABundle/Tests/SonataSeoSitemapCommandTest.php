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

class SonataSeoSitemapCommandTest extends CommandTestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testMissingArguments()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "sonata:seo:sitemap");
    }

    public function testGenerate()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "sonata:seo:sitemap --baseurl=/fr web sonata.local");

        $this->assertContains('done!', $output);
        $this->assertContains('Generating sitemap - this can take a while', $output);
        $this->assertContains('Moving temporary file to web ...', $output);

        $baseFolder = $client->getContainer()->getParameter('kernel.root_dir');

        $this->assertFileExists(sprintf("%s/../web/sitemap.xml", $baseFolder));
        $this->assertFileExists(sprintf("%s/../web/sitemap_00001.xml", $baseFolder));

        new \SimpleXMLElement(file_get_contents(sprintf("%s/../web/sitemap.xml", $baseFolder)));
        new \SimpleXMLElement(file_get_contents(sprintf("%s/../web/sitemap_00001.xml", $baseFolder)));
    }
}