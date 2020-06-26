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

class SonataPageUpdateCoreRouteCommandTest extends CommandTestCase
{
    public function testMissingArguments()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, 'sonata:page:update-core-routes');

        $this->assertStringContainsString('Please provide an --site=SITE_ID', $output);
    }

    public function testUpdateOneSite()
    {
        $client = self::createClient();

        $site = $client->getContainer()->get('sonata.page.manager.site')->findOneBy([]);

        $output = $this->runCommand($client, sprintf('sonata:page:update-core-routes --site=%s', $site->getId()));

        $this->assertStringContainsString('Updating core routes for site', $output);
        $this->assertStringContainsString('done!', $output);
    }

    public function testUpdateSites()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, sprintf("sonata:page:update-core-routes --site=all --base-command='php %s'", $this->getConsoleLocation($client)));

        $this->assertStringContainsString('Updating core routes for site', $output);
        $this->assertStringContainsString('done!', $output);
    }
}
