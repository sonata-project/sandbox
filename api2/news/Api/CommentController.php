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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CommentController
{
    /**
     * @var CommentManagerInterface
     */
    protected $commentManager;

    /**
     * @param CommentManagerInterface $commentManager Comment manager
     */
    public function __construct(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * Retrieves a specific comment.
     *
     * @ApiDoc(
     *  resource=true,
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Comment identifier"}
     *  },
     *  output={"class"="Sonata\NewsBundle\Model\Comment", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when comment is not found"
     *  }
     * )
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Comment identifier
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Comment identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when comment is successfully deleted",
     *      400="Returned when an error has occurred while comment deletion",
     *      404="Returned when unable to find comment"
     *  }
     * )
     *
     * @param string $id Comment identifier
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
     * @param string $id Comment identifier
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
