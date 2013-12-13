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

class SonataMediaSyncCommandTest extends CommandTestCase
{

    /**
     * @dataProvider getMediaList
     */
    public function testRefresh($id)
    {
        $client = self::createClient();

        $output = $this->runCommand($client, sprintf("sonata:media:sync-thumbnails %s %s",
            $id,
            'default'
        ));

        $this->assertContains("Done.", $output);
    }
}