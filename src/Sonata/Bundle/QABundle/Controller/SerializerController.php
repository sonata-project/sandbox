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

class SerializerController extends Controller
{

    /**
     * This make sure there is no regression to retrieve the current website in a sub request
     *
     * reference: https://github.com/sonata-project/SonataPageBundle/pull/211
     *
     * @return Response
     */
    public function serializeAction(Request $request)
    {
        $raw = array(
            'json' => 'no data available',
            'xml' => 'no data available',
        );

        if ($request->isMethod('POST')) {
            $class = $request->get('class');

            if ($request->get('id')) {
                $object = $this->getDoctrine()->getRepository($class)->find($request->get('id'));
            } else {
                $object = $this->getDoctrine()->getRepository($class)->findOneBy(array());
            }

            $raw = array(
                'json' => $this->get('jms_serializer')->serialize($object, 'json'),
                'xml' => $this->get('jms_serializer')->serialize($object, 'xml'),
            );
        }

        $metas = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();

        $classes = array();
        foreach($metas as $name => $meta) {
            if ($meta->reflClass->isAbstract()) {
                continue;
            }

            $classes[] = $meta->name;
        }

        return $this->render('SonataQABundle:Serializer:serialize.html.twig', array(
            'classes' => $classes,
            'raw'     => $raw
        ));
    }
}