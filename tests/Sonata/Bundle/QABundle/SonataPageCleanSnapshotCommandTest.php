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

class SonataPageCleanSnapshotCommandTest extends CommandTestCase
{
    public function testRefresh()
    {
        $this->markTestSkipped('The sqlite database platform has not been tested yet.');

        $client = self::createClient();

        $output = $this->runCommand($client, sprintf('sonata:page:cleanup-snapshots --site=all --base-console=%s', $this->getConsoleLocation($client)));

        $this->assertStringContainsString('done!', $output);
    }
}
