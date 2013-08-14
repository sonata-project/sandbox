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

class SonataPageCreateSiteCommandTest extends CommandTestCase
{

    public function testCreateSite()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "sonata:page:create-site ".
            " --no-confirmation=true".
            " --enabled=true".
            " --name=Test".
            " --host=test.localhost".
            " --enabledFrom=now".
            " --enabledTo=now".
            " --locale=fr_FR".
            " --relativePath=/".
            " --default=false".
            " --no-interaction"
        );

        $this->assertContains("Creating website with the following information :", $output);
        $this->assertContains("Site created !", $output);
    }
}