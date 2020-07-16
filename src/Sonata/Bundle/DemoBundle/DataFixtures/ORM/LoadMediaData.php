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

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use AppBundle\Entity\Media\GalleryHasMedia;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\GalleryManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\Finder\Finder;

class LoadMediaData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var GalleryManagerInterface
     */
    private $galleryManager;

    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    public function __construct(Generator $faker, GalleryManagerInterface $galleryManager, MediaManagerInterface $mediaManager)
    {
        $this->faker = $faker;
        $this->galleryManager = $galleryManager;
        $this->mediaManager = $mediaManager;
    }

    public function getOrder(): int
    {
        return 3;
    }

    public function load(ObjectManager $manager): void
    {
        $gallery = $this->galleryManager->create();

        $canada = Finder::create()->name('IMG_3587*.jpg')->in(__DIR__.'/../data/files/gilles-canada');
        $paris = Finder::create()->name('IMG_3008*.jpg')->in(__DIR__.'/../data/files/hugo-paris');
        $switzerland = Finder::create()->name('switzerland_2012-05-19_006.jpg')->in(__DIR__.'/../data/files/sylvain-switzerland');

        $i = 0;
        foreach ($canada as $file) {
            $media = $this->mediaManager->create();
            $media->setBinaryContent(__DIR__.'/../data/files/gilles-canada/'.$file->getRelativePathname());
            $media->setEnabled(true);
            $media->setName('Canada');
            $media->setDescription('Canada');
            $media->setAuthorName('Gilles Rosenbaum');
            $media->setCopyright('CC BY-NC-SA 4.0');
            $media->setCategory($this->getReference('travels_quebec_category'));

            $this->addReference('sonata-media-'.($i++), $media);

            $this->mediaManager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMediaToGallery($media, $gallery);
        }

        foreach ($paris as $file) {
            $media = $this->mediaManager->create();
            $media->setBinaryContent(__DIR__.'/../data/files/hugo-paris/'.$file->getRelativePathname());
            $media->setEnabled(true);
            $media->setName('Paris');
            $media->setDescription('Paris');
            $media->setAuthorName('Hugo Briand');
            $media->setCopyright('CC BY-NC-SA 4.0');
            $media->setCategory($this->getReference('travels_paris_category'));

            $this->addReference('sonata-media-'.($i++), $media);

            $this->mediaManager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMediaToGallery($media, $gallery);
        }

        foreach ($switzerland as $file) {
            $media = $this->mediaManager->create();
            $media->setBinaryContent(__DIR__.'/../data/files/sylvain-switzerland/'.$file->getRelativePathname());
            $media->setEnabled(true);
            $media->setName('Switzerland');
            $media->setDescription('Switzerland');
            $media->setAuthorName('Sylvain Deloux');
            $media->setCopyright('CC BY-NC-SA 4.0');
            $media->setCategory($this->getReference('travels_switzerland_category'));

            $this->addReference('sonata-media-'.($i++), $media);

            $this->mediaManager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMediaToGallery($media, $gallery);
        }

        $gallery->setEnabled(true);
        $gallery->setName($this->faker->sentence(4));
        $gallery->setDefaultFormat('small');
        $gallery->setContext('default');

        $this->galleryManager->update($gallery);

        $this->addReference('media-gallery', $gallery);
    }

    public function addMediaToGallery(MediaInterface $media, GalleryInterface $gallery): void
    {
        $galleryHasMedia = new GalleryHasMedia();
        $galleryHasMedia->setMedia($media);
        $galleryHasMedia->setPosition(\count($gallery->getGalleryHasMedias()) + 1);
        $galleryHasMedia->setEnabled(true);

        $gallery->addGalleryHasMedias($galleryHasMedia);
    }
}
