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

class SonataEasyExtendsDumpMappingCommandTest extends CommandTestCase
{
    public function testDumpException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $client = self::createClient();
        $this->runCommand($client, 'sonata:easy-extends:dump-mapping');
    }

    /**
     * @dataProvider getAdminList
     * @doesNotPerformAssertions
     *
     * @param $id
     * @param $class
     */
    public function testDumpInformation($id, $class)
    {
        $client = self::createClient();
        $this->runCommand($client, sprintf('sonata:easy-extends:dump-mapping default "%s"', str_replace('\\', '\\\\', $class)));
    }
}
