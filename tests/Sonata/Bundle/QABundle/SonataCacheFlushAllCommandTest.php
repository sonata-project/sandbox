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

class SonataCacheFlushAllCommandTest extends CommandTestCase
{
    public function testFlushAll()
    {
        static::markTestSkipped('The "sonata.cache.manager" service or alias has been removed or inlined when the container was compiled.');

        $client = self::createClient();
        $output = $this->runCommand($client, 'sonata:cache:flush-all', false);

        static::assertNotNull($output);
        static::assertStringNotContainsString('FAILED!', $output);
    }
}
