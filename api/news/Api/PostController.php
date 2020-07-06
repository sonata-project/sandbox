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

use Application\Sonata\NewsBundle\Entity\Post;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Mailer\MailerInterface;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
 */
class PostController
{
    /**
     * @var PostManagerInterface
     */
    protected $postManager;

    /**
     * @var CommentManagerInterface
     */
    protected $commentManager;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FormatterPool
     */
    protected $formatterPool;

    public function __construct(PostManagerInterface $postManager, CommentManagerInterface $commentManager, MailerInterface $mailer, FormFactoryInterface $formFactory, FormatterPool $formatterPool)
    {
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
        $this->mailer = $mailer;
        $this->formFactory = $formFactory;
        $this->formatterPool = $formatterPool;
    }

    /**
     * Retrieves the list of posts (paginated) based on criteria.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the list of posts (paginated) based on criteria.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for posts list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of posts by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enabled/Disabled posts filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="dateQuery",
     *         in="query",
     *         description="Date filter orientation (>, < or =)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="dateValue",
     *         in="query",
     *         description="Date filter value",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Tag name filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="author",
     *         in="query",
     *         description="Author filter",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="mode",
     *         in="query",
     *         description="'public' mode filters posts having enabled tags and author",
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
     * @Rest\Get("/posts.{_format}", name="get_posts")
     *
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page for posts list pagination")
     * @REST\QueryParam(name="count", requirements="\d+", default="10", description="Number of posts by page")
     * @REST\QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled posts filter")
     * @REST\QueryParam(name="dateQuery", requirements=">|<|=", default=">", description="Date filter orientation (>, < or =)")
     * @REST\QueryParam(name="dateValue", requirements="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-2][0-9]:[0-5][0-9]:[0-5][0-9]([+-][0-9]{2}(:)?[0-9]{2})?", nullable=true, strict=true, description="Date filter value")
     * @REST\QueryParam(name="tag", requirements="\S+", nullable=true, strict=true, description="Tag name filter")
     * @REST\QueryParam(name="author", requirements="\S+", nullable=true, strict=true, description="Author filter")
     * @REST\QueryParam(name="mode", requirements="public|admin", default="public", description="'public' mode filters posts having enabled tags and author")
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getPostsAction(ParamFetcherInterface $paramFetcher)
    {
        $page = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        $pager = $this->postManager->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $pager;
    }

    /**
     * Retrieves a specific post.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves a specific post.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_news_api_form_post"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when post is not found"
     *     )
     * )
     *
     *
     * @Rest\Get("/posts/{id}.{_format}", name="get_post")
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param int $id A post identifier
     *
     * @return Post
     */
    public function getPostAction($id)
    {
        return $this->getPost($id);
    }

    /**
     * Adds a post.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a post.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_news_api_form_post"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while post creation"
     *     )
     * )
     *
     *
     * @Rest\Post("/posts.{_format}", name="post_post")
     *
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return Post
     */
    public function postPostAction(Request $request)
    {
        return $this->handleWritePost($request);
    }

    /**
     * Updates a post.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a post.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="sonata_news_api_form_post"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while post update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find post"
     *     )
     * )
     *
     *
     * @Rest\Put("/posts/{id}.{_format}", name="put_post")
     *
     * @param int     $id      A Post identifier
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return Post
     */
    public function putPostAction($id, Request $request)
    {
        return $this->handleWritePost($request, $id);
    }

    /**
     * Deletes a post.
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes a post.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when post is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while post deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find post"
     *     )
     * )
     *
     *
     * @Rest\Delete("/posts/{id}.{_format}", name="delete_post")
     *
     * @param int $id A Post identifier
     *
     * @throws NotFoundHttpException
     *
     * @return View
     */
    public function deletePostAction($id)
    {
        $post = $this->getPost($id);

        try {
            $this->postManager->delete($post);
        } catch (\Exception $e) {
            return View::create(['error' => $e->getMessage()], 400);
        }

        return ['deleted' => true];
    }

    /**
     * Retrieves the comments of specified post.
     *
     * @Operation(
     *     tags={""},
     *     summary="Retrieves the comments of specified post.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for comments list pagination",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of comments by page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\DatagridBundle\Pager\PagerInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when post is not found"
     *     )
     * )
     *
     *
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page for comments list pagination")
     * @REST\QueryParam(name="count", requirements="\d+", default="10", description="Number of comments by page")
     *
     * @Rest\Post("/posts/{id}/comments.{_format}", name="get_post_comments")
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param int $id A post identifier
     *
     * @return PagerInterface
     */
    public function getPostCommentsAction($id, ParamFetcherInterface $paramFetcher)
    {
        $post = $this->getPost($id);

        $page = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        $criteria = $this->filterCriteria($paramFetcher);
        $criteria['postId'] = $post->getId();

        /** @var PagerInterface $pager */
        $pager = $this->commentManager->getPager($criteria, $page, $count);

        return $pager;
    }

    /**
     * Adds a comment to a post.
     *
     * @Operation(
     *     tags={""},
     *     summary="Adds a comment to a post.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\NewsBundle\Model\Comment"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while comment creation"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when commenting is not enabled on the related post"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when post is not found"
     *     )
     * )
     *
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @Rest\Post("/posts/{id}/comments.{_format}", name="post_post_comments")
     *
     * @param int $id A post identifier
     *
     * @throws HttpException
     *
     * @return Comment|FormInterface
     */
    public function postPostCommentsAction($id, Request $request)
    {
        $post = $this->getPost($id);

        if (!$post->isCommentable()) {
            throw new HttpException(403, sprintf('Post (%d) not commentable', $id));
        }

        $comment = $this->commentManager->create();
        $comment->setPost($post);

        $form = $this->formFactory->createNamed(null, 'sonata_news_api_form_comment', $comment, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);

            if (!$comment->getStatus()) {
                $comment->setStatus($post->getCommentsDefaultStatus());
            }

            $this->commentManager->save($comment);
            $this->mailer->sendCommentNotification($comment);

            return $comment;
        }

        return $form;
    }

    /**
     * Updates a comment.
     *
     * @Operation(
     *     tags={""},
     *     summary="Updates a comment.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\NewsBundle\Model\Comment"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while comment update"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find comment"
     *     )
     * )
     *
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @Rest\Put("/posts/{postId}/comments/{commentId}.{_format}", name="put_post_comments")
     *
     * @param int     $postId    A post identifier
     * @param int     $commentId A comment identifier
     * @param Request $request   A Symfony request
     *
     * @throws NotFoundHttpException
     * @throws HttpException
     *
     * @return Comment
     */
    public function putPostCommentsAction($postId, $commentId, Request $request)
    {
        $post = $this->getPost($postId);

        if (!$post->isCommentable()) {
            throw new HttpException(403, sprintf('Post (%d) not commentable', $postId));
        }

        $comment = $this->commentManager->find($commentId);

        if (null === $comment) {
            throw new NotFoundHttpException(sprintf('Comment (%d) not found', $commentId));
        }

        $comment->setPost($post);

        $form = $this->formFactory->createNamed(null, 'sonata_news_api_form_comment', $comment, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment = $form->getData();
            $this->commentManager->save($comment);

            return $comment;
        }

        return $form;
    }

    /**
     * Filters criteria from $paramFetcher to be compatible with the Pager criteria.
     *
     * @return array The filtered criteria
     */
    protected function filterCriteria(ParamFetcherInterface $paramFetcher)
    {
        $criteria = $paramFetcher->all();

        unset($criteria['page'], $criteria['count']);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (\array_key_exists('dateValue', $criteria)) {
            $date = new \DateTime($criteria['dateValue']);
            $criteria['date'] = [
                'query' => sprintf('p.publicationDateStart %s :dateValue', $criteria['dateQuery']),
                'params' => ['dateValue' => $date],
            ];
            unset($criteria['dateValue'], $criteria['dateQuery']);
        } else {
            unset($criteria['dateQuery']);
        }

        return $criteria;
    }

    /**
     * Retrieves post with id $id or throws an exception if it doesn't exist.
     *
     * @param int $id A Post identifier
     *
     * @throws NotFoundHttpException
     *
     * @return Post
     */
    protected function getPost($id)
    {
        $post = $this->postManager->find($id);

        if (null === $post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        return $post;
    }

    /**
     * Write a post, this method is used by both POST and PUT action methods.
     *
     * @param Request  $request Symfony request
     * @param int|null $id      A post identifier
     *
     * @return FormInterface
     */
    protected function handleWritePost($request, $id = null)
    {
        $post = $id ? $this->getPost($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_news_api_form_post', $post, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $post = $form->getData();
            $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
            $this->postManager->save($post);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            // simplify when dropping FOSRest < 2.1
            if (method_exists($context, 'enableMaxDepth')) {
                $context->enableMaxDepth();
            } else {
                $context->setMaxDepth(10);
            }

            $view = View::create($post);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
