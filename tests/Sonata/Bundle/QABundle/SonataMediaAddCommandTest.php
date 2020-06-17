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

class SonataMediaAddCommandTest extends CommandTestCase
{
    public function testException()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();
        $this->runCommand($client, 'sonata:media:add');
    }

    public function testMediaAdd()
    {
        $client = self::createClient();
        $baseFolder = $client->getContainer()->getParameter('kernel.root_dir');

        $output = $this->runCommand($client, sprintf('sonata:media:add %s %s %s',
            'sonata.media.provider.image',
            'product_catalog',
            sprintf('%s/../src/Sonata/Bundle/DemoBundle/DataFixtures/data/files/IMG_0003.JPG', $baseFolder)
        ));

        $this->assertStringContainsString('Add a new media - context: product_catalog, provider: sonata.media.provider.image, content: ', $output);
        $this->assertStringContainsString('done!', $output);
    }
}
