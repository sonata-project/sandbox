<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!is_file('composer.json')) {
    throw new \RuntimeException('This script must be started from the project root folder');
}

$rootDir = __DIR__ . '/..';

require_once __DIR__ . '/../app/bootstrap.php.cache';

use Symfony\Component\Console\Output\OutputInterface;

// reset data
$fs = new \Symfony\Component\Filesystem\Filesystem;
$output = new \Symfony\Component\Console\Output\ConsoleOutput();

// does the parent directory have a parameters.yml file
if (is_file(__DIR__.'/../../parameters.demo.yml')) {
    $fs->copy(__DIR__.'/../../parameters.demo.yml', __DIR__.'/../app/config/parameters.yml', true);
}

if (!is_file(__DIR__.'/../app/config/parameters.yml')) {
    $output->writeln('<error>no default apps/config/parameters.yml file</error>');

    exit(1);
}

/**
 * @param $commands
 * @param \Symfony\Component\Console\Output\ConsoleOutput $output
 *
 * @return boolean
 */
function execute_commands($commands, $output)
{
    foreach($commands as $command) {
        list($command, $message) = $command;


        $output->write(sprintf(' - %\'.-70s', $message));
        $p = new \Symfony\Component\Process\Process($command);
        $p->setTimeout(null);
        $return = array();
        $p->run(function($type, $data) use (&$return) {
            $return[] = $data;
        });

        if (!$p->isSuccessful()) {
            $output->writeln('<error>KO</error>');
            $output->writeln(sprintf('<error>Fail to run: %s</error>', $command));
            foreach($return as $data) {
               $output->write($data, false, OutputInterface::OUTPUT_RAW);
            }

            $output->writeln("If the error is coming from the sandbox,");
            $output->writeln("please report the issue to https://github.com/sonata-project/sandbox/issues");
            return false;
        }

        $output->writeln("<info>OK</info>");
    }

    return true;
}

// find out the default php runtime
$bin = 'php';

if (defined('PHP_BINARY')) {
    $bin = PHP_BINARY;
}

$output->writeln(<<<SONATA
                                       __
               _________  ____  _____ / /______
              / ___/ __ \/ __ \/ __  / __/ __  /
             (__  ) /_/ / / / / /_/ / /_/ /_/ /
            /____/\____/_/ /_/\__,_/\__/\__,_/

SONATA
);
$output->writeln("<info>Resetting demo, this can take a few minutes</info>");

$fs->remove(sprintf('%s/web/uploads/media', $rootDir));
$fs->mkdir(sprintf('%s/web/uploads/media', $rootDir));

$fs->copy(__DIR__.'/../src/Sonata/Bundle/DemoBundle/DataFixtures/data/robots.txt', __DIR__.'/../web/robots.txt', true);

$success = execute_commands(array(
    array($bin . ' ./bin/sonata-check.php','Checking Sonata Project\'s requirements'),
    array('rm -rf ./app/cache/*','Cleaning the cache'),
    array($bin . ' ./app/console cache:warmup --env=prod --no-debug','Warming up the production cache'),
    array($bin . ' ./app/console cache:create-cache-class --env=prod --no-debug','Creating the class cache'),
    array($bin . ' ./app/console doctrine:database:drop --force','Dropping the database'),
    array($bin . ' ./app/console doctrine:database:create','Creating the database'),
    array($bin . ' ./app/console doctrine:schema:update --force','Creating the database\'s schema'),
    array($bin . '  -d memory_limit=1024M -d max_execution_time=600 ./app/console doctrine:fixtures:load --verbose --env=dev --no-debug','Loading fixtures'),
    array($bin . ' ./app/console sonata:news:sync-comments-count','SonataNewsBundle: updating comments count'),
    array($bin . ' ./app/console sonata:page:update-core-routes --site=all --no-debug','SonataPageBundle: updating core route'),
    array($bin . ' ./app/console sonata:page:create-snapshots --site=all --no-debug','SonataPageBundle: creating snapshots from pages'),
    array($bin . ' ./app/console assets:install --symlink web','Configure assets'),
    array($bin . ' ./app/console sonata:admin:setup-acl','Security: setting up ACL'),
    array($bin . '  -d memory_limit=1024M ./app/console sonata:admin:generate-object-acl','Security: generating object ACL'),
), $output);

if (!$success) {
    $output->writeln('<info>An error occurs when running a command!</info>');

    exit(1);
}

$output->writeln('');
$output->writeln('<info>What\'s next ?!</info>');
$output->writeln(sprintf(' - Configure your webserver to point to the %s/web folder.', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..')));
$output->writeln(' - Review the documentation: https://sonata-project.org/bundles');
$output->writeln(' - Follow us on twitter: https://twitter.com/sonataproject');

exit(0);
