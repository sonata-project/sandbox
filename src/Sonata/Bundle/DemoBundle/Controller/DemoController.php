<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sonata\Bundle\DemoBundle\Model\MediaPreview;
use Symfony\Component\HttpFoundation\Request;

class DemoController extends Controller
{
    public function mediaAction(Request $request)
    {
        // preset a default value
        $media = $this->get('sonata.media.manager.media')->create();
        $media->setBinaryContent('http://www.youtube.com/watch?v=qTVfFmENgPU');

        // create the target object
        $mediaPreview = new MediaPreview();
        $mediaPreview->setMedia($media);

        // create the form
        $builder = $this->createFormBuilder($mediaPreview);
        $builder->add('media', 'sonata_media_type', array(
             'provider' => 'sonata.media.provider.youtube',
             'context'  => 'default'
        ));

        $form = $builder->getForm();

        // bind and transform the media's binary content into real content
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            $this->getSeoPage()
                ->setTitle($media->getName())
                ->addMeta('property', 'og:description', $media->getDescription())
                ->addMeta('property', 'og:type', 'video')
            ;
        }

        return $this->render('SonataDemoBundle:Demo:media.html.twig', array(
            'form' => $form->createView(),
            'media' => $mediaPreview->getMedia()
        ));
    }

    /**
     * @return \Sonata\SeoBundle\Seo\SeoPageInterface
     */
    public function getSeoPage()
    {
        return $this->get('sonata.seo.page');
    }
}

