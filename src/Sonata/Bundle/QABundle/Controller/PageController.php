<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\QABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{

    /**
     * This make sure there is no regression to retrieve the current website in a sub request
     *
     * reference: https://github.com/sonata-project/SonataPageBundle/pull/211
     *
     * @return Response
     */
    public function controllerHelperAction()
    {
        return $this->render('SonataQABundle:Page:controllerHelper.html.twig', array(
            'site' => $this->get('sonata.page.site.selector')->retrieve()
        ));
    }

    /**
     * @return Response
     */
    public function internalControllerAction(Request $request)
    {
        $site = $this->get('sonata.page.site.selector')->retrieve();

        return new Response(sprintf("<pre>The sub request current site name is: %s (url: %s)</pre> <br /><pre>%s</pre>",
            $site->getName(),
            $this->generateUrl('sonata_page_bundle_inner_controller'),
            $request
        ));
    }
}