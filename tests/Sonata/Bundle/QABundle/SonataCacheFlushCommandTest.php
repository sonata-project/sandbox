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

class SonataCacheFlushCommandTest extends CommandTestCase
{
    public function testFlushException()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();
        $this->runCommand($client, 'sonata:cache:flush');
    }

    /**
     * @dataProvider getCacheList
     *
     * @param $id
     * @param $keys
     */
    public function testFlush($id, $keys)
    {
        static::markTestSkipped('The "sonata.cache.manager" service or alias has been removed or inlined when the container was compiled.');

        $client = self::createClient();
        $this->runCommand($client, sprintf('sonata:cache:flush --cache=%s --keys=%s', $id, $keys));
    }
}
