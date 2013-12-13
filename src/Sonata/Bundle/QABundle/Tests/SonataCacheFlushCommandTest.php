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

class SonataCacheFlushCommandTest extends CommandTestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testFlushException()
    {
        $client = self::createClient();
        $this->runCommand($client, "sonata:cache:flush");
    }

    /**
     * @dataProvider getCacheList
     *
     * @param $id
     * @param $keys
     */
    public function testFlush($id, $keys)
    {
        $client = self::createClient();
        $this->runCommand($client, sprintf("sonata:cache:flush --cache=%s --keys=%s", $id, $keys));
    }
}