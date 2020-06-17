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

class SonataNotificationRestartCommandTest extends CommandTestCase
{
    public function testRefresh()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, 'sonata:notification:restart');

        $this->assertStringContainsString('Nothing to restart, bye.', $output);
    }
}
