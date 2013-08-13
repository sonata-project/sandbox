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

class SonataAdminExplainCommandTest extends CommandTestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testExplainException()
    {
        $client = self::createClient();
        $output = $this->runCommand($client, "sonata:admin:explain");
    }

    /**
     * @dataProvider getAdminList
     */
    public function testExplain($id, $class)
    {
        $client = self::createClient();

        $output = $this->runCommand($client, sprintf("sonata:admin:explain %s", $id));

        $this->assertNotNull($output);
    }
}