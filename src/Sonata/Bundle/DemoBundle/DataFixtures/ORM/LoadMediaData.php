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
use Sonata\ClassificationBundle\Model\Category;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\GalleryManagerInterface;
use Sonata\MediaBundle\Model\Media;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

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
        $homepageGallery = $this->galleryManager->create();

        $media = $this->createMedia('gilles-canada/IMG_3587.jpg', 'Canada', 'Gilles Rosenbaum', $this->getReference('travels_quebec_category'));
        $this->addMediaToGallery($media, $homepageGallery);
        $this->setReference('sonata-media-0', $media);

        $media = $this->createMedia('hugo-paris/IMG_3008.jpg', 'Paris', 'Hugo Briand', $this->getReference('travels_paris_category'));
        $this->addMediaToGallery($media, $homepageGallery);

        $media = $this->createMedia('sylvain-switzerland/switzerland_2012-05-19_006.jpg', 'Switzerland', 'Sylvain Deloux', $this->getReference('travels_switzerland_category'));
        $this->addMediaToGallery($media, $homepageGallery);

        // disabled media
        $media = $this->createMedia('hugo-paris/IMG_2571.jpg', 'Paris 2', 'Hugo Briand', $this->getReference('travels_paris_category'), false);
        $this->addMediaToGallery($media, $homepageGallery);

        // disabled GalleryHasMedia
        $media = $this->createMedia('hugo-paris/IMG_2577.jpg', 'Paris 3', 'Hugo Briand', $this->getReference('travels_paris_category'));
        $this->addMediaToGallery($media, $homepageGallery, false);

        $homepageGallery->setEnabled(true);
        $homepageGallery->setName('Homepage gallery');
        $homepageGallery->setDefaultFormat('small');
        $homepageGallery->setContext('default');

        $this->galleryManager->update($homepageGallery);

        $this->addReference('media-homepage-gallery', $homepageGallery);
    }

    public function addMediaToGallery(MediaInterface $media, GalleryInterface $gallery, bool $enabled = true): void
    {
        $galleryHasMedia = new GalleryHasMedia();
        $galleryHasMedia->setMedia($media);
        $galleryHasMedia->setPosition(\count($gallery->getGalleryHasMedias()) + 1);
        $galleryHasMedia->setEnabled($enabled);

        $gallery->addGalleryHasMedias($galleryHasMedia);
    }

    /**
     * @param string|File $file File pathname or `Symfony\Component\HttpFoundation\File` object
     *
     * @return Media
     */
    protected function createMedia($file, string $name, string $autor, Category $category, bool $enabled = true, string $copyright = 'CC BY-NC-SA 4.0')
    {
        $media = $this->mediaManager->create();
        $media->setBinaryContent(__DIR__.'/../data/files/'.$file);
        $media->setEnabled($enabled);
        $media->setName($name);
        $media->setDescription($name);
        $media->setAuthorName($autor);
        $media->setCopyright($copyright);
        $media->setCategory($category);

        $this->mediaManager->save($media, 'default', 'sonata.media.provider.image');

        return $media;
    }
}
