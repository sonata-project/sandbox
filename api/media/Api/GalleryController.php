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

namespace Sonata\MediaBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View as FOSRestView;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\MediaBundle\Form\Type\ApiGalleryHasMediaType;
use Sonata\MediaBundle\Form\Type\ApiGalleryType;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\GalleryManagerInterface;
use Sonata\MediaBundle\Model\GalleryMediaCollectionInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @final since sonata-project/media-bundle 3.21.0
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class GalleryController
{
    /**
     * @var GalleryManagerInterface
     */
    protected $galleryManager;

    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $galleryHasMediaClass;

    /**
     * Constructor.
     *
     * @param string $galleryHasMediaClass
     */
    public function __construct(GalleryManagerInterface $galleryManager, MediaManagerInterface $mediaManager, FormFactoryInterface $formFactory, $galleryHasMediaClass)
    {
        $this->galleryManager = $galleryManager;
        $this->mediaManager = $mediaManager;
        $this->formFactory = $formFactory;
        $this->galleryHasMediaClass = $galleryHasMediaClass;
    }

    /**
     * Retrieves the list of galleries (paginated).
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the list of galleries (paginated).",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for gallery list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of galleries by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/Disabled galleries filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Order by array (key is field, value is direction)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\DatagridBundle\Pager\PagerInterface"))
     *     )
     * )
     *
     *
     * @Rest\Get("/galleries", name="get_galleries")
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for gallery list pagination")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of galleries by page")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled galleries filter")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Order by array (key is field, value is direction)")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getGalleriesAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'enabled' => '',
        ];

        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('count');
        $sort = $paramFetcher->get('orderBy');
        $criteria = array_intersect_key($paramFetcher->all(), $supportedCriteria);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (!$sort) {
            $sort = [];
        } elseif (!\is_array($sort)) {
            $sort = [$sort => 'asc'];
        }

        return $this->getGalleryManager()->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_media_api_form_gallery"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when gallery is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/galleries/{id}", name="get_gallery")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return GalleryInterface
     */
    public function getGalleryAction($id)
    {
        return $this->getGallery($id);
    }

    /**
     * Retrieves the medias of specified gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the medias of specified gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\MediaBundle\Model\Media"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when gallery is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/galleries/{id}/medias", name="get_gallery_medias")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return MediaInterface[]
     */
    public function getGalleryMediasAction($id)
    {
        $ghms = $this->getGallery($id)->getGalleryHasMedias();

        $media = [];
        foreach ($ghms as $ghm) {
            $media[] = $ghm->getMedia();
        }

        return $media;
    }

    /**
     * Retrieves the galleryhasmedias of specified gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the galleryhasmedias of specified gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\MediaBundle\Model\GalleryHasMedia"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when gallery is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/galleries/{id}/galleryhasmedias", name="get_gallery_galleryhasmedias")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return GalleryHasMediaInterface[]
     */
    public function getGalleryGalleryhasmediasAction($id)
    {
        return $this->getGallery($id)->getGalleryHasMedias();
    }

    /**
     * Adds a gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_media_api_form_gallery"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while gallery creation"
     *     )
     * )
     *
     *
     * @Rest\Post("/galleries", name="post_gallery")
     *
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return GalleryInterface
     */
    public function postGalleryAction(Request $request)
    {
        return $this->handleWriteGallery($request);
    }

    /**
     * Updates a gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_media_api_form_gallery"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while gallery creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find gallery"
     *     )
     * )
     *
     *
     * @Rest\Put("/galleries/{id}", name="put_gallery")
     *
     * @param int     $id      User id
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return GalleryInterface
     */
    public function putGalleryAction($id, Request $request)
    {
        return $this->handleWriteGallery($request, $id);
    }

    /**
     * Adds a media to a gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a media to a gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_media_api_form_gallery"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while gallery/media attachment"
     *     )
     * )
     *
     *
     * @Rest\Post("/galleries/{galleryId}/media/{mediaId}/galleryhasmedia", name="post_gallery_media_galleryhasmedia")
     *
     * @param int     $galleryId A gallery identifier
     * @param int     $mediaId   A media identifier
     * @param Request $request   A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return GalleryInterface
     */
    public function postGalleryMediaGalleryhasmediaAction($galleryId, $mediaId, Request $request)
    {
        $gallery = $this->getGallery($galleryId);
        $media = $this->getMedia($mediaId);

        foreach ($gallery->getGalleryHasMedias() as $galleryHasMedia) {
            if ($galleryHasMedia->getMedia()->getId() === $media->getId()) {
                return FOSRestView::create([
                    'error' => sprintf('Gallery "%s" already has media "%s"', $galleryId, $mediaId),
                ], 400);
            }
        }

        return $this->handleWriteGalleryhasmedia($gallery, $media, null, $request);
    }

    /**
     * Updates a media to a gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a media to a gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_media_api_form_gallery"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when an error if media cannot be found in gallery"
     *     )
     * )
     *
     *
     * @Rest\Put("/galleries/{galleryId}/media/{mediaId}/galleryhasmedia", name="put_gallery_media_galleryhasmedia")
     *
     * @param int     $galleryId A gallery identifier
     * @param int     $mediaId   A media identifier
     * @param Request $request   A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return GalleryInterface
     */
    public function putGalleryMediaGalleryhasmediaAction($galleryId, $mediaId, Request $request)
    {
        $gallery = $this->getGallery($galleryId);
        $media = $this->getMedia($mediaId);

        foreach ($gallery->getGalleryHasMedias() as $galleryHasMedia) {
            if ($galleryHasMedia->getMedia()->getId() === $media->getId()) {
                return $this->handleWriteGalleryhasmedia($gallery, $media, $galleryHasMedia, $request);
            }
        }

        throw new NotFoundHttpException(sprintf('Gallery "%s" does not have media "%s"', $galleryId, $mediaId));
    }

    /**
     * Deletes a media association to a gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a media association to a gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when media is successfully deleted from gallery"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while media deletion of gallery"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find gallery or media"
     *     )
     * )
     *
     *
     * @Rest\Delete("/galleries/{galleryId}/media/{mediaId}/galleryhasmedia", name="delete_gallery_media_galleryhasmedia")
     *
     * @param int $galleryId A gallery identifier
     * @param int $mediaId   A media identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deleteGalleryMediaGalleryhasmediaAction($galleryId, $mediaId)
    {
        $gallery = $this->getGallery($galleryId);
        $media = $this->getMedia($mediaId);

        foreach ($gallery->getGalleryHasMedias() as $key => $galleryHasMedia) {
            if ($galleryHasMedia->getMedia()->getId() === $media->getId()) {
                $gallery->getGalleryHasMedias()->remove($key);
                $this->getGalleryManager()->save($gallery);

                return ['deleted' => true];
            }
        }

        return FOSRestView::create([
            'error' => sprintf('Gallery "%s" does not have media "%s" associated', $galleryId, $mediaId),
        ], 400);
    }

    /**
     * Deletes a gallery.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a gallery.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when gallery is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while gallery deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find gallery"
     *     )
     * )
     *
     *
     * @Rest\Delete("/galleries/{id}", name="delete_gallery")
     *
     * @param int $id A Gallery identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deleteGalleryAction($id)
    {
        $gallery = $this->getGallery($id);

        $this->galleryManager->delete($gallery);

        return ['deleted' => true];
    }

    /**
     * Write a GalleryHasMedia, this method is used by both POST and PUT action methods.
     *
     * @param GalleryHasMediaInterface $galleryHasMedia
     *
     * @return FormInterface
     */
    protected function handleWriteGalleryhasmedia(GalleryInterface $gallery, MediaInterface $media, ?GalleryHasMediaInterface $galleryHasMedia = null, Request $request)
    {
        $form = $this->formFactory->createNamed(null, ApiGalleryHasMediaType::class, $galleryHasMedia, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $galleryHasMedia = $form->getData();
            $galleryHasMedia->setMedia($media);

            // NEXT_MAJOR: remove this if/else block. Just call `$gallery->addGalleryHasMedia($galleryHasMedia);`
            if ($gallery instanceof GalleryMediaCollectionInterface) {
                $gallery->addGalleryHasMedia($galleryHasMedia);
            } else {
                $gallery->addGalleryHasMedias($galleryHasMedia);
            }
            $this->galleryManager->save($gallery);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);
            $context->enableMaxDepth();

            $view = FOSRestView::create($galleryHasMedia);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }

    /**
     * Retrieves gallery with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     *
     * @return GalleryInterface
     */
    protected function getGallery($id)
    {
        $gallery = $this->getGalleryManager()->findOneBy(['id' => $id]);

        if (null === $gallery) {
            throw new NotFoundHttpException(sprintf('Gallery (%d) not found', $id));
        }

        return $gallery;
    }

    /**
     * Retrieves media with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     *
     * @return MediaInterface
     */
    protected function getMedia($id)
    {
        $media = $this->getMediaManager()->findOneBy(['id' => $id]);

        if (null === $media) {
            throw new NotFoundHttpException(sprintf('Media (%d) not found', $id));
        }

        return $media;
    }

    /**
     * @return GalleryManagerInterface
     */
    protected function getGalleryManager()
    {
        return $this->galleryManager;
    }

    /**
     * @return MediaManagerInterface
     */
    protected function getMediaManager()
    {
        return $this->mediaManager;
    }

    /**
     * Write a Gallery, this method is used by both POST and PUT action methods.
     *
     * @param Request  $request Symfony request
     * @param int|null $id      A Gallery identifier
     *
     * @return View|FormInterface
     */
    protected function handleWriteGallery($request, $id = null)
    {
        $gallery = $id ? $this->getGallery($id) : null;

        $form = $this->formFactory->createNamed(null, ApiGalleryType::class, $gallery, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $gallery = $form->getData();
            $this->galleryManager->save($gallery);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);
            $context->enableMaxDepth();

            $view = FOSRestView::create($gallery);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
