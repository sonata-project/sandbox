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

use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SerializerController extends AbstractController
{
    /**
     * This make sure there is no regression to retrieve the current website in a sub request.
     *
     * reference: https://github.com/sonata-project/SonataPageBundle/pull/211
     *
     * @return Response
     */
    public function serializeAction(Request $request)
    {
        $raw = [
            'json' => 'no data available',
            'xml' => 'no data available',
        ];

        if ($request->isMethod('POST')) {
            $class = $request->get('class');

            if ($request->get('id')) {
                $object = $this->getDoctrine()->getRepository($class)->find($request->get('id'));
            } else {
                $object = $this->getDoctrine()->getRepository($class)->findOneBy([]);
            }

            $serializationContext = SerializationContext::create();

            $serializationContext->enableMaxDepthChecks();

            if ($request->get('group')) {
                $serializationContext->setGroups([$request->get('group')]);
            }

            if ($request->get('version')) {
                $serializationContext->setVersion($request->get('version'));
            }

            $jsonSerializationContext = $serializationContext;
            $xmlSerializationContext = clone $serializationContext;

            $raw = [
                'json' => $this->get('jms_serializer')->serialize($object, 'json', $jsonSerializationContext),
                'xml' => $this->get('jms_serializer')->serialize($object, 'xml', $xmlSerializationContext),
            ];
        }

        $metas = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();

        $classes = [];
        foreach ($metas as $name => $meta) {
            if ($meta->reflClass->isAbstract()) {
                continue;
            }

            $classes[] = $meta->name;
        }

        return $this->render('@SonataQA/Serializer/serialize.html.twig', [
            'classes' => $classes,
            'raw' => $raw,
        ]);
    }
}
