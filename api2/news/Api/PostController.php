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

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Mailer\MailerInterface;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\Post;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
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
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Post identifier"}
     *  },
     *  output={"class"="sonata_news_api_form_post", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when post is not found"
     *  }
     * )
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Post identifier
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
     * @ApiDoc(
     *  input={"class"="sonata_news_api_form_post", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="sonata_news_api_form_post", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while post creation",
     *  }
     * )
     *
     * @param Request $request Symfony request
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Post identifier"}
     *  },
     *  input={"class"="sonata_news_api_form_post", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="sonata_news_api_form_post", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while post update",
     *      404="Returned when unable to find post"
     *  }
     * )
     *
     * @param string  $id      Post identifier
     * @param Request $request Symfony request
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Post identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when post is successfully deleted",
     *      400="Returned when an error has occurred while post deletion",
     *      404="Returned when unable to find post"
     *  }
     * )
     *
     * @param string $id Post identifier
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Post identifier"}
     *  },
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when post is not found"
     *  }
     * )
     *
     * @REST\QueryParam(name="page", requirements="\d+", default="1", description="Page for comments list pagination")
     * @REST\QueryParam(name="count", requirements="\d+", default="10", description="Number of comments by page")
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Post identifier
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Post identifier"}
     *  },
     *  input={"class"="sonata_news_api_form_comment", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\NewsBundle\Model\Comment", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while comment creation",
     *      403="Returned when commenting is not enabled on the related post",
     *      404="Returned when post is not found"
     *  }
     * )
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Post identifier
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="postId", "dataType"="string", "description"="Post identifier"},
     *      {"name"="commentId", "dataType"="string", "description"="Comment identifier"}
     *  },
     *  input={"class"="sonata_news_api_form_comment", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\NewsBundle\Model\Comment", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while comment update",
     *      404="Returned when unable to find comment"
     *  }
     * )
     *
     * @REST\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string  $postId    Post identifier
     * @param string  $commentId Comment identifier
     * @param Request $request   Symfony request
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
     * @param string $id Post identifier
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
     * @param Request     $request Symfony request
     * @param string|null $id      Post identifier
     *
     * @return View|FormInterface
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
