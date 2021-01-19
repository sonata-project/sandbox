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

if (!is_file('composer.json')) {
    throw new \RuntimeException('Can\'t find a composer.json file. Make sure to start this script from the project root folder');
}

$checks = [];
if (extension_loaded('gd')) {
    $checks[] = ['OK', 'gd is installed'];

    $info = gd_info();

    if ($info['JPEG Support']) {
        $checks[] = ['OK', 'gd with jpg support is installed'];
    } else {
        $checks[] = ['KO', 'missing gd with jpg support'];
    }

    if ($info['PNG Support']) {
        $checks[] = ['OK', 'gd with png support is installed'];
    } else {
        $checks[] = ['KO', 'missing png with jpg support'];
    }
} else {
    $checks[] = ['KO', 'gd is not installed'];
}

if (class_exists('Locale')) {
    $checks[] = ['OK', 'php-intl extension is installed'];
} else {
    $checks[] = ['KO', 'Missing php-intl extension'];
}

if (is_file('app/check.php')) {
    // find out the default php runtime
    $bin = 'php';

    if (defined('PHP_BINARY')) {
        $bin = \PHP_BINARY;
    }

    exec(sprintf('%s app/check.php', $bin), $output, $exit);

    if (1 === $exit) {
        $checks[] = ['KO', "Failed to valid Symfony2's requirements!"];

        foreach ($output as $line) {
            $checks[] = [' ->', $line];
        }
    } else {
        $checks[] = ['OK', "Successfully valid Symfony2's requirements"];
    }
}

if (!function_exists('mb_strlen')) {
    $checks[] = ['KO', 'Install and enable the <strong>mbstring</strong> extension.'];
} else {
    $checks[] = ['OK', 'mb_strlen() is available'];
}

if (!class_exists('PDO')) {
    $checks[] = ['KO', 'PDO must be installed'];
} else {
    $checks[] = ['OK', 'PDO is available'];

    $drivers = PDO::getAvailableDrivers();

    if (!in_array('mysql', $drivers, true)) {
        $checks[] = ['KO', 'PDO mysql is not available'];
    } else {
        $checks[] = ['OK', 'PDO mysql is available'];
    }
}

if (!function_exists('bcscale')) {
    $checks[] = ['KO', 'bcmath extension is not available'];
} else {
    $checks[] = ['OK', 'bcmath extension is available'];
}

if (!function_exists('curl_init')) {
    $checks[] = ['KO', 'curl extension is not available'];
} else {
    $checks[] = ['OK', 'curl extension is available'];
}

// xdebug is optional
if (extension_loaded('xdebug') && ini_get('xdebug.max_nesting_level') < 255) {
    $checks[] = ['KO', 'xdebug.max_nesting_level is too low, please make sure the value is greater than 255'];
}

$error = 0;

foreach ($checks as $check) {
    echo sprintf("%s\t%s\n", $check[0], $check[1]);

    if ('KO' === $check[0]) {
        $error = 1;
    }
}

echo "\n";

if ($error) {
    echo "Failed to validate mandatory requirements\n";
} else {
    echo "Successfully validate mandatory requirements\n";
}

exit($error);
