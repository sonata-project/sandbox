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
use Sonata\Bundle\DemoBundle\Entity\MediaPreview;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sonata\Bundle\DemoBundle\Form\Type\CarType;

class DemoController extends Controller
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carAction(Request $request)
    {
        $form = $this->createForm(new CarType());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }

        ob_start();
        var_dump($form->getData());
        $dump = ob_get_contents();
        ob_clean();

        return $this->render('SonataDemoBundle:Demo:car.html.twig', array(
            'form' => $form->createView(),
            'dump' => $dump
        ));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function carRescueEngineAction(Request $request)
    {
        $car = new \Sonata\Bundle\DemoBundle\Entity\Car();
        $car->setName('Poney Car');
        $car->setCreatedAt(new \DateTime);

        $rescueEngines = array(
            1 => new \Sonata\Bundle\DemoBundle\Entity\Engine('Rescue 1', 100),
            2 => new \Sonata\Bundle\DemoBundle\Entity\Engine('Rescue 2', 125),
            3 => new \Sonata\Bundle\DemoBundle\Entity\Engine('Rescue 3', 150),
        );

        $form = $this->createForm('sonata_demo_form_type_car', $car, array(
            'rescue_engines' => $rescueEngines
        ));

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }

        ob_start();
        var_dump($car);
        $dump = ob_get_contents();
        ob_clean();


        return $this->render('SonataDemoBundle:Demo:car.html.twig', array(
            'form' => $form->createView(),
            'dump' => $dump
        ));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mediaAction(Request $request)
    {
        // preset a default value
        $media = $this->get('sonata.media.manager.media')->create();
        $media->setBinaryContent('http://www.youtube.com/watch?v=oHg5SJYRHA0');

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
            $form->bind($request);

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
     * Newsletter subscription action
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsletterAction(Request $request)
    {
        $form = $this->createForm('sonata_demo_form_type_newsletter');
        $form->handleRequest($request);

        $message = 'sonata.demo.block.newsletter.message';

        return $this->render('SonataDemoBundle:Block:newsletter_confirmation.html.twig', array(
            'message' => $form->isValid() ? $message : null
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

