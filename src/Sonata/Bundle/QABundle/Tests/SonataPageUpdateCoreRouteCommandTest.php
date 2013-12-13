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

class SonataPageUpdateCoreRouteCommandTest extends CommandTestCase
{
    public function testMissingArguments()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "sonata:page:update-core-routes");

        $this->assertContains('Please provide an --site=SITE_ID', $output);
    }

    public function testUpdateOneSite()
    {
        $client = self::createClient();

        $site = $client->getContainer()->get('sonata.page.manager.site')->findOneBy(array());

        $output = $this->runCommand($client, sprintf("sonata:page:update-core-routes --site=%s", $site->getId()));

        $this->assertContains("Updating core routes for site", $output);
        $this->assertContains("done!", $output);
    }

    public function testUpdateSites()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, sprintf("sonata:page:update-core-routes --site=all --base-command='php %s'", $this->getConsoleLocation($client)));

        $this->assertContains("Updating core routes for site", $output);
        $this->assertContains("done!", $output);
    }
}
