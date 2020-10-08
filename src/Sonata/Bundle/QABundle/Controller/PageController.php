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

namespace Sonata\Bundle\QABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
{
    /**
     * This make sure there is no regression to retrieve the current website in a sub request.
     *
     * reference: https://github.com/sonata-project/SonataPageBundle/pull/211
     */
    public function controllerHelperAction(): Response
    {
        return $this->render('@SonataQA/Page/controllerHelper.html.twig', [
            'site' => $this->get('sonata.page.site.selector')->retrieve(),
        ]);
    }

    public function internalControllerAction(Request $request): Response
    {
        $site = $this->get('sonata.page.site.selector')->retrieve();

        return new Response(sprintf(
            '<pre>The sub request current site name is: %s (url: %s)</pre> <br /><pre>%s</pre>',
            $site->getName(),
            $this->generateUrl('sonata_page_bundle_inner_controller'),
            $request
        ));
    }
}
