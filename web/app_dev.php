<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../app/AppKernel.php';

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}


//use Symfony\Component\HttpFoundation\Request;

// if you want to use the SonataPageBundle with multisite
// using different relative paths, you must change the request
// object to use the SiteRequest
use Sonata\PageBundle\Request\SiteRequest as Request;

$request = Request::createFromGlobals();

$kernel = new AppKernel('dev', true);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
