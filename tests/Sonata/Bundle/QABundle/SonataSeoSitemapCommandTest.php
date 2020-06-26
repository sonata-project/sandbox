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

class SonataSeoSitemapCommandTest extends CommandTestCase
{
    public function testMissingArguments()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();

        $output = $this->runCommand($client, 'sonata:seo:sitemap');
    }

    public function testGenerate()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, sprintf('sonata:seo:sitemap --baseurl=/fr %s/../web sonata.local', $client->getContainer()->getParameter('kernel.root_dir')));

        $this->assertStringContainsString('done!', $output);
        $this->assertStringContainsString('Generating sitemap - this can take a while', $output);
        $this->assertStringContainsString('Moving temporary file to', $output);

        $baseFolder = $client->getContainer()->getParameter('kernel.root_dir');

        $this->assertFileExists(sprintf('%s/../web/sitemap.xml', $baseFolder));
        $this->assertFileExists(sprintf('%s/../web/sitemap_00001.xml', $baseFolder));

        new \SimpleXMLElement(file_get_contents(sprintf('%s/../web/sitemap.xml', $baseFolder)));
        new \SimpleXMLElement(file_get_contents(sprintf('%s/../web/sitemap_00001.xml', $baseFolder)));
    }
}
