<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$rootDir = __DIR__;

require_once __DIR__.'/apps/bootstrap.php.cache';

// reset data
$fs = new \Symfony\Component\Filesystem\Filesystem;
$output = new \Symfony\Component\Console\Output\ConsoleOutput();

// does the parent directory have a parameters.yml file
if (is_file(__DIR__.'/../parameters.demo.yml')) {
    $fs->copy(__DIR__.'/../parameters.demo.yml', __DIR__.'/apps/config/parameters.yml', true);
}

if (!is_file(__DIR__.'/apps/config/parameters.yml')) {
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
        $output->writeln(sprintf('<info>Executing : </info> %s', $command));
        $p = new \Symfony\Component\Process\Process($command);
        $p->setTimeout(null);
        $p->run(function($type, $data) use ($output) {
            $output->write($data);
        });

        if (!$p->isSuccessful()) {
            return false;
        }

        $output->writeln("");
    }

    return true;
}

$output->writeln("<info>Resetting demo</info>");

$fs->remove(sprintf('%s/web/uploads/media', $rootDir));
$fs->mkdir(sprintf('%s/web/uploads/media', $rootDir));

$fs->copy(__DIR__.'/src/Sonata/Bundle/DemoBundle/DataFixtures/data/robots.txt', __DIR__.'/web/app/robots.txt', true);

$success = execute_commands(array(
    'rm -rf apps/api/cache/*',
    'rm -rf apps/app/cache/*',
    'apps/api/console cache:warmup --env=prod --no-debug',
    'apps/app/console cache:warmup --env=prod --no-debug',
    'apps/app/console cache:create-cache-class --env=prod --no-debug',
    'apps/app/console doctrine:database:drop --force',
    'apps/app/console doctrine:database:create',
    'apps/app/console doctrine:schema:update --force',
    'apps/app/console doctrine:fixtures:load --verbose',
    'apps/app/console sonata:page:update-core-routes --site=all --no-debug --base-command="apps/app/console"',
    'apps/app/console sonata:page:create-snapshots --site=all --no-debug --base-console="apps/app/console"',
    'apps/app/console assets:install --symlink web',
    'apps/app/console cache:create-cache-class --env=prod --no-debug',
    'apps/app/console sonata:admin:setup-acl',
    'php -d memory_limit=1024M apps/app/console sonata:admin:generate-object-acl'
), $output);

if (!$success) {
    $output->writeln('<info>An error occurs when running a command!</info>');

    exit(1);
}

$output->writeln('<info>Done!</info>');

exit(0);
