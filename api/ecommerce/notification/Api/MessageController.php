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

namespace Sonata\NotificationBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\NotificationBundle\Model\MessageInterface;
use Sonata\NotificationBundle\Model\MessageManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @Route(defaults={"_format": "json"}, requirements={"_format": "json|xml|html"})
 */
class MessageController
{
    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(MessageManagerInterface $messageManager, FormFactoryInterface $formFactory)
    {
        $this->messageManager = $messageManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Retrieves the list of messages (paginated).
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
     *
     * @Rest\Get("/messages.{_format}", name="get_messages")
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for message list pagination")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of messages by page")
     * @Rest\QueryParam(name="type", nullable=true, description="Message type filter")
     * @Rest\QueryParam(name="state", requirements="\d+", strict=true, nullable=true, description="Message status filter")
     * @Rest\QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query groups order by clause (key is field, value is direction)")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getMessagesAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = [
            'state' => '',
            'type' => '',
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

        return $this->getMessageManager()->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Adds a message.
     *
     * @ApiDoc(
     *  input={"class"="sonata_notification_api_form_message", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\NotificationBundle\Model\Message", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while message creation"
     *  }
     * )
     *
     * @Rest\Post("/messages.{_format}", name="post_message")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request A Symfony request
     *
     * @return MessageInterface
     */
    public function postMessageAction(Request $request)
    {
        $message = null;

        $form = $this->formFactory->createNamed(null, 'sonata_notification_api_form_message', $message, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = $form->getData();
            $this->messageManager->save($message);

            return $message;
        }

        return $form;
    }

    /**
     * @return MessageManagerInterface
     */
    protected function getMessageManager()
    {
        return $this->messageManager;
    }
}
