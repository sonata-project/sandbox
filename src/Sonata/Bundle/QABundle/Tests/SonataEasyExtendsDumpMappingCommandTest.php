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

class SonataEasyExtendsDumpMappingCommandTest extends CommandTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDumpException()
    {
        $client = self::createClient();
        $this->runCommand($client, "sonata:easy-extends:dump-mapping");
    }

    /**
     * @dataProvider getAdminList
     *
     * @param $id
     * @param $class
     */
    public function testDumpInformation($id, $class)
    {
        $client = self::createClient();
        $this->runCommand($client, sprintf("sonata:easy-extends:dump-mapping default \"%s\"", str_replace('\\', '\\\\', $class)));
    }
}