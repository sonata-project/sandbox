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

class SonataAdminListCommandTest extends CommandTestCase
{
    public function testListing()
    {
        $client = self::createClient();
        $output = $this->runCommand($client, 'sonata:admin:list');

        $this->assertNotNull($output);

        foreach (self::getAdminList() as $def) {
            [$id, $class] = $def;

            $this->assertStringContainsString($id, $output);
            $this->assertStringContainsString($class, $output);
        }
    }
}
