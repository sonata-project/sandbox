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

class SonataUserTwoStepVerificationCommandTest extends CommandTestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testMissingArguments()
    {
        $client = self::createClient();

        $this->runCommand($client, "sonata:user:two-step-verification");
    }

    public function testReset()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "sonata:user:two-step-verification --reset secure");

        $this->assertContains("Url : https://www.google.com/", $output);

        $user = $client->getContainer()->get('fos_user.user_manager')->findUserBy(array(
            'username' => 'secure'
        ));

        $this->assertContains($user->getTwoStepVerificationCode(), $output);
    }

    public function testGenerateOnGenerateUser()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "sonata:user:two-step-verification secure");

        $code = $client->getContainer()->get('fos_user.user_manager')->findUserBy(array(
            'username' => 'secure'
        ))->getTwoStepVerificationCode();

        $this->assertContains($code, $output);
    }
}