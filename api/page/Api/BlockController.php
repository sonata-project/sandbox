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

namespace Sonata\PageBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Model\BlockManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
 */
class BlockController extends FOSRestController
{
    /**
     * @var BlockManagerInterface
     */
    protected $blockManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(BlockManagerInterface $blockManager, FormFactoryInterface $formFactory)
    {
        $this->blockManager = $blockManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Retrieves a specific block.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific block.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\PageBundle\Model\BlockInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when page is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/blocks/{id}.{_format}", name="get_block")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return BlockInterface
     */
    public function getBlockAction($id)
    {
        return $this->getBlock($id);
    }

    /**
     * Updates a block.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a block.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\PageBundle\Model\Block"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while block creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find page"
     *     )
     * )
     *
     *
     * @Rest\Put("/blocks/{id}.{_format}", name="put_block")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param int     $id      A Block identifier
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return BlockInterface
     */
    public function putBlockAction($id, Request $request)
    {
        $block = $id ? $this->getBlock($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_page_api_form_block', $block, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $block = $form->getData();

            $this->blockManager->save($block);

            return $block;
        }

        return $form;
    }

    /**
     * Deletes a block.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a block.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when block is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while block deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find block"
     *     )
     * )
     *
     *
     * @Rest\Delete("/blocks/{id}.{_format}", name="delete_block")
     *
     * @param int $id A Block identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteBlockAction($id)
    {
        $block = $this->getBlock($id);

        $this->blockManager->delete($block);

        return ['deleted' => true];
    }

    /**
     * Retrieves Block with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     *
     * @return BlockInterface
     */
    protected function getBlock($id)
    {
        $block = $this->blockManager->findOneBy(['id' => $id]);

        if (null === $block) {
            throw new NotFoundHttpException(sprintf('Block (%d) not found', $id));
        }

        return $block;
    }
}
