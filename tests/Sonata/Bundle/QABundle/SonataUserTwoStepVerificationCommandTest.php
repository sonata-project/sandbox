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

class SonataUserTwoStepVerificationCommandTest extends CommandTestCase
{
    public function testMissingArguments()
    {
        $this->expectException(\RuntimeException::class);

        $client = self::createClient();

        $this->runCommand($client, 'sonata:user:two-step-verification');
    }

    public function testReset()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, 'sonata:user:two-step-verification --reset secure');

        $this->assertStringContainsString('Url : https://chart.googleapis.com/', $output);

        $user = $client->getContainer()->get('fos_user.user_manager')->findUserBy([
            'username' => 'secure',
        ]);

        $this->assertStringContainsString($user->getTwoStepVerificationCode(), $output);
    }

    public function testGenerateOnGenerateUser()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, 'sonata:user:two-step-verification secure');

        $code = $client->getContainer()->get('fos_user.user_manager')->findUserBy([
            'username' => 'secure',
        ])->getTwoStepVerificationCode();

        $this->assertStringContainsString($code, $output);
    }
}
