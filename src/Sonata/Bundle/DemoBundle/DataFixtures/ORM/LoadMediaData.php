<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class LoadMediaData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function getOrder()
    {
        return 3;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $gallery = $this->getGalleryManager()->create();

        $mediaManager = $this->getMediaManager();
        $faker = $this->getFaker();

        $canada = Finder::create()->name('IMG_3587*.jpg')->in(__DIR__.'/../data/files/gilles-canada');
        $paris = Finder::create()->name('IMG_3008*.jpg')->in(__DIR__.'/../data/files/hugo-paris');
        $switzerland = Finder::create()->name('switzerland_2012-05-19_006.jpg')->in(__DIR__.'/../data/files/sylvain-switzerland');

        $i = 0;
        foreach ($canada as $file) {
            $media = $mediaManager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setName('Canada');
            $media->setDescription('Canada');
            $media->setAuthorName('Gilles Rosenbaum');
            $media->setCopyright('CC BY-NC-SA 4.0');
            $media->setCategory($this->getReference('travels_quebec_category'));

            $this->addReference('sonata-media-'.($i++), $media);

            $mediaManager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMedia($gallery, $media);
        }

        foreach ($paris as $file) {
            $media = $mediaManager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setName('Paris');
            $media->setDescription('Paris');
            $media->setAuthorName('Hugo Briand');
            $media->setCopyright('CC BY-NC-SA 4.0');
            $media->setCategory($this->getReference('travels_paris_category'));

            $this->addReference('sonata-media-'.($i++), $media);

            $mediaManager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMedia($gallery, $media);
        }

        foreach ($switzerland as $file) {
            $media = $mediaManager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setName('Switzerland');
            $media->setDescription('Switzerland');
            $media->setAuthorName('Sylvain Deloux');
            $media->setCopyright('CC BY-NC-SA 4.0');
            $media->setCategory($this->getReference('travels_switzerland_category'));

            $this->addReference('sonata-media-'.($i++), $media);

            $mediaManager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMedia($gallery, $media);
        }

        $gallery->setEnabled(true);
        $gallery->setName($faker->sentence(4));
        $gallery->setDefaultFormat('small');
        $gallery->setContext('default');

        $this->getGalleryManager()->update($gallery);

        $this->addReference('media-gallery', $gallery);
    }

    /**
     * @param \Sonata\MediaBundle\Model\GalleryInterface $gallery
     * @param \Sonata\MediaBundle\Model\MediaInterface   $media
     */
    public function addMedia(GalleryInterface $gallery, MediaInterface $media)
    {
        $galleryHasMedia = new \AppBundle\Entity\Media\GalleryHasMedia();
        $galleryHasMedia->setMedia($media);
        $galleryHasMedia->setPosition(count($gallery->getGalleryHasMedias()) + 1);
        $galleryHasMedia->setEnabled(true);

        $gallery->addGalleryHasMedias($galleryHasMedia);
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getGalleryManager()
    {
        return $this->container->get('sonata.media.manager.gallery');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }
}
