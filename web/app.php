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

//use Symfony\Component\HttpFoundation\Request;

// if you want to use the SonataPageBundle with multisite
// using different relative paths, you must change the request
// object to use the SiteRequest
use Sonata\PageBundle\Request\SiteRequest as Request;

$request = Request::createFromGlobals();

$kernel = new AppKernel('prod', false);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
