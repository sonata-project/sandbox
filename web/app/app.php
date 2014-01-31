<?php

require_once __DIR__.'/../../apps/app/bootstrap.php.cache';
require_once __DIR__.'/../../apps/app/AppKernel.php';
//require_once __DIR__.'/../../apps/app/AppCache.php';

//$kernel = new AppCache(new AppKernel('prod', false));
$kernel = new AppKernel('prod', false);
//$kernel->loadClassCache();

// if you want to use the SonataPageBundle with multisite
// using different relative paths, you must change the request
// object to use the SiteRequest
use Sonata\PageBundle\Request\SiteRequest as Request;

//use Symfony\Component\HttpFoundation\Request;

$kernel->handle(Request::createFromGlobals())->send();