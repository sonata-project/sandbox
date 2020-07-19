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

class SonataAdminExplainCommandTest extends CommandTestCase
{
    public function testExplainException()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();
        $this->runCommand($client, 'sonata:admin:explain');
    }

    /**
     * @dataProvider getAdminList
     */
    public function testExplain($id, $class)
    {
        $client = self::createClient();

        $output = $this->runCommand($client, sprintf('sonata:admin:explain %s', $id));

        $this->assertNotNull($output, sprintf('Fail to assert admin id: %s with class: %s', $id, $class));
    }
}
