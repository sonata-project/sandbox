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
    throw new \RuntimeException('Can\'t find a composer.json file. Make sure to start this script from the project root folder');
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
        list($command, $message, $allowFailure, $timeoutDuration) = $command;

        $output->write(sprintf(' - %\'.-70s', $message));
        $return = array();
        if (is_callable($command)) {
            $success = $command($output);
        } else {
            try {
                $p = new \Symfony\Component\Process\Process($command);
                $p->setTimeout($timeoutDuration);
                $p->run(function($type, $data) use (&$return) {
                    $return[] = $data;
                });

                $success = $p->isSuccessful();
                $timeout = false;
            } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $ex){
                $success = false;
                $timeout = true;
            }
        }

        if (!$success && !$allowFailure) {
            if($timeout){
                $output->writeln('<error>KO(timeout)</error>');
            }else{
                $output->writeln('<error>KO</error>');
            }
            $output->writeln(sprintf('<error>Fail to run: %s</error>', is_callable($command) ? '[closure]' : $command));
            foreach($return as $data) {
               $output->write($data, false, OutputInterface::OUTPUT_RAW);
            }

            $output->writeln("If the error is coming from the sandbox,");
            $output->writeln("please report the issue to https://github.com/sonata-project/sandbox/issues");

            return false;
        } else if (!$success) {
            if($timeout){
                $output->writeln('<error>!!(timeout)</error>');
            }else{
                $output->writeln('<error>!!</error>');
            }
        } else {
            $output->writeln("<info>OK</info>");
        }
    }

    return true;
}

$output->writeln(<<<SONATA
                                       __
               _________  ____  _____ / / ______
              / ___/ __ \/ __ \/ __  / __/ __  /
             (__  ) /_/ / / / / /_/ / /_/ /_/ /
            /____/\____/_/ /_/\__,_/\__/\__,_/
SONATA
);
$output->writeln("");
$output->writeln("<info>Resetting demo, this can take a few minutes</info>");

$fs->remove(sprintf('%s/web/uploads/media', $rootDir));
$fs->mkdir(sprintf('%s/web/uploads/media', $rootDir));

// find out the default php runtime
$bin = sprintf("%s -d memory_limit=-1", defined('PHP_BINARY') ? PHP_BINARY: 'php');


if (extension_loaded('xdebug')) {
    $output->writeln("<error>WARNING, xdebug is enabled in the cli, this can drastically slowing down all PHP scripts</error>");
}

$success = execute_commands(array(
    array($bin . ' ./bin/sonata-check.php','Checking Sonata Project\'s requirements', false, null),
    array(function(OutputInterface $output) use ($fs) {
        $fs->remove("app/cache/prod");
        $fs->remove("app/cache/dev");

        return true;
    }, 'Deleting prod and dev cache folders', false, null),
    array(function(OutputInterface $output) use ($fs) {
        return $fs->exists("app/config/parameters.yml");
    }, 'Check for app/config/parameters.yml file', false, null),
    array($bin . ' ./app/console cache:create-cache-class --env=prod --no-debug','Creating the class cache', false, null),
    array($bin . ' ./app/console doctrine:database:drop --force','Dropping the database', true, null),
    array($bin . ' ./app/console doctrine:database:create','Creating the database', false, null),
    array($bin . ' ./app/console doctrine:schema:update --force','Creating the database\'s schema', false, null),
    array($bin . ' ./app/console doctrine:fixtures:load --verbose --env=dev --no-debug --no-interaction','Loading fixtures', false, 600),
    array($bin . ' ./app/console sonata:news:sync-comments-count','Sonata - News: updating comments count', false, null),
    array($bin . ' ./app/console sonata:page:update-core-routes --site=all --no-debug','Sonata - Page: updating core route', false, null),
    array($bin . ' ./app/console sonata:page:create-snapshots --site=all --no-debug','Sonata - Page: creating snapshots from pages', false, null),
    array($bin . ' ./app/console assets:install --symlink web','Configure assets', false, null),
    array($bin . ' ./app/console sonata:admin:setup-acl','Security: setting up ACL', false, null),
    array($bin . ' ./app/console sonata:admin:generate-object-acl','Security: generating object ACL', false, null),
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
