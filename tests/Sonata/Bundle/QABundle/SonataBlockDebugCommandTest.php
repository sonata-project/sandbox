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

class SonataBlockDebugCommandTest extends CommandTestCase
{
    public function testFlushAll()
    {
        $client = self::createClient();
        $output = $this->runCommand($client, 'sonata:block:debug');

        static::assertNotNull($output);

        foreach (self::getBlockList() as $def) {
            [$id] = $def;

            static::assertStringContainsString($id, $output);
        }
    }
}
