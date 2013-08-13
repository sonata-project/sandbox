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

class SonataAdminListCommandTest extends CommandTestCase
{
    public function testListing()
    {
        $client = self::createClient();
        $output = $this->runCommand($client, "sonata:admin:list");

        $this->assertNotNull($output);

        foreach (self::getAdminList() as $def) {
            list($id, $class) = $def;

            $this->assertContains($id, $output);
            $this->assertContains($class, $output);
        }
    }
}