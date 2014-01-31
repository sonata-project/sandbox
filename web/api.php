<?php

die("You are not allowed to access to the API. Check ".basename(__FILE__)." for more information.");

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__ . '/../app/ApiKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

//$kernel = new AppCache(new ApiKernel('prod', false));
$kernel = new ApiKernel('prod', false);
//$kernel->loadClassCache();

// if you want to use the SonataPageBundle with multisite
// using different relative paths, you must change the request
// object to use the SiteRequest
use Sonata\PageBundle\Request\SiteRequest as Request;

//use Symfony\Component\HttpFoundation\Request;

$kernel->handle(Request::createFromGlobals())->send();