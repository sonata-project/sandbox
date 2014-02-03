<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../apps/bootstrap.php.cache';
require_once __DIR__.'/../apps/BaseKernel.php';

//use Symfony\Component\HttpFoundation\Request;

// if you want to use the SonataPageBundle with multisite
// using different relative paths, you must change the request
// object to use the SiteRequest
use Sonata\PageBundle\Request\SiteRequest as Request;

/**
 * @param Request $request
 *
 * @return string
 */
function sonata_get_app(Request $request) {
    $paths = explode("/", trim($request->getPathInfo(), "/"));

    $app = count($paths) > 0 ? $paths[0] : 'front';

    // You can add new app here
    if (!in_array($app, array('api', 'admin'))) {
        $app = 'front';
    }

    return $app;
}

/**
 * @param string  $name
 * @param string  $env
 * @param boolean $debug
 *
 * @return Symfony\Component\HttpKernel\KernelInterface
 */
function sonata_bootstrap($name, $env, $debug) {

    $className = ucfirst($name).'Kernel';

    require_once __DIR__.'/../apps/'.$name.'/'.$className.'.php';

    $kernel = new $className($env, $debug);

    return $kernel;
}

/**
 * @param $env
 * @param $debug
 *
 * @return Symfony\Component\HttpFoundation\Response
 */
function sonata_handle($env, $debug) {

    $request = Request::createFromGlobals();

    $kernel = sonata_bootstrap(sonata_get_app($request), $env, $debug);

    return $kernel->handle($request);
}