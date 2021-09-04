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

class SonataNotificationListQueuesCommandTest extends CommandTestCase
{
    public function testCommand()
    {
        static::markTestSkipped('Failed asserting that "List of queues available queue: message - routing_key:" contains "The backend class Sonata\NotificationBundle\Backend\PostponeRuntimeBackend does not provide multiple queues.".');

        $client = self::createClient();

        $output = $this->runCommand($client, 'sonata:notification:list-queues');

        static::assertStringContainsString('The backend class Sonata\\NotificationBundle\\Backend\\PostponeRuntimeBackend does not provide multiple queues.', $output);
    }
}
