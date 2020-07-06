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

namespace Sonata\NewsBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
 */
class CommentController
{
    /**
     * @var CommentManagerInterface
     */
    protected $commentManager;

    /**
     * @param CommentManagerInterface $commentManager A comment manager
     */
    public function __construct(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * Retrieves a specific comment.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific comment.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\NewsBundle\Model\Comment"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when comment is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/comments/{id}.{_format}", name="get_comment")
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param int $id A comment identifier
     *
     * @throws NotFoundHttpException
     *
     * @return Comment
     */
    public function getCommentAction($id)
    {
        return $this->getComment($id);
    }

    /**
     * Deletes a comment.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a comment.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when comment is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while comment deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find comment"
     *     )
     * )
     *
     *
     * @Rest\Delete("/comments/{id}.{_format}", name="delete_comment")
     *
     * @param int $id A comment identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deleteCommentAction($id)
    {
        $comment = $this->getComment($id);

        try {
            $this->commentManager->delete($comment);
        } catch (\Exception $e) {
            return View::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Returns a comment entity instance.
     *
     * @param int $id A comment identifier
     *
     * @throws NotFoundHttpException
     *
     * @return Comment
     */
    protected function getComment($id)
    {
        $comment = $this->commentManager->find($id);

        if (null === $comment) {
            throw new NotFoundHttpException(sprintf('Comment (%d) not found', $id));
        }

        return $comment;
    }
}
