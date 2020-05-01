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

namespace Sonata\Bundle\DemoBundle\Controller;

use Sonata\Bundle\DemoBundle\Entity\Engine;
use Sonata\Bundle\DemoBundle\Entity\MediaPreview;
use Sonata\Bundle\DemoBundle\Entity\Peugeot;
use Sonata\Bundle\DemoBundle\Form\Type\NewsletterType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DemoController extends AbstractController
{
    public function carAction(Request $request): Response
    {
        $form = $this->createForm('sonata_demo_form_type_engine');

        $form->handleRequest($request);

        if ('POST' === $request->getMethod()) {
            if ($form->isValid() && $form->isSubmitted()) {
                // do something
            }
        }

        ob_start();
        var_dump($form->getData());
        $dump = ob_get_contents();
        ob_clean();

        return $this->render('@SonataDemo/Demo/car.html.twig', [
            'form' => $form->createView(),
            'dump' => $dump,
        ]);
    }

    public function carRescueEngineAction(Request $request): Response
    {
        $car = new Peugeot();
        $car->setName('Poney Car');
        $car->setCreatedAt(new \DateTime());

        $rescueEngines = [
            1 => new Engine('Rescue 1', 100),
            2 => new Engine('Rescue 2', 125),
            3 => new Engine('Rescue 3', 150),
        ];

        $form = $this->createForm('sonata_demo_form_type_car', $car, [
            'rescue_engines' => $rescueEngines,
        ]);

        $form->handleRequest($request);

        if ('POST' === $request->getMethod()) {
            if ($form->isValid() && $form->isSubmitted()) {
                // do something
            }
        }

        ob_start();
        var_dump($car);
        $dump = ob_get_contents();
        ob_clean();

        return $this->render('@SonataDemo/Demo/car.html.twig', [
            'form' => $form->createView(),
            'dump' => $dump,
        ]);
    }

    public function mediaAction(Request $request): Response
    {
        // preset a default value
        $media = $this->get('sonata.media.manager.media')->create();
        $media->setBinaryContent('http://www.youtube.com/watch?v=oHg5SJYRHA0');

        // create the target object
        $mediaPreview = new MediaPreview();
        $mediaPreview->setMedia($media);

        // create the form
        $builder = $this->createFormBuilder($mediaPreview);
        $builder->add('media', MediaType::class, [
             'provider' => 'sonata.media.provider.youtube',
             'context' => 'default',
        ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        // bind and transform the media's binary content into real content
        if ('POST' === $request->getMethod()) {
            if ($form->isValid() && $form->isSubmitted()) {
                $this->getSeoPage()
                    ->setTitle($media->getName())
                    ->addMeta('property', 'og:description', $media->getDescription())
                    ->addMeta('property', 'og:type', 'video');
            }
        }

        return $this->render('@SonataDemo/Demo/media.html.twig', [
            'form' => $form->createView(),
            'media' => $mediaPreview->getMedia(),
        ]);
    }

    public function newsletterAction(Request $request): Response
    {
        $form = $this->createForm(NewsletterType::class);
        $form->handleRequest($request);

        $message = 'sonata.demo.block.newsletter.message';

        return $this->render('@SonataDemo/Block/newsletter_confirmation.html.twig', [
            'message' => $form->isValid() ? $message : null,
        ]);
    }

    private function getSeoPage(): SeoPageInterface
    {
        return $this->get('sonata.seo.page');
    }
}
