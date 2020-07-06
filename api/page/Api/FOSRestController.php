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

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View as FOSRestView;

/**
 * @author Duchkina Anastasiya <duchkina.nast@gmail.com>
 */
abstract class FOSRestController
{
    /**
     * NEXT_MAJOR: Remove this method, as it should be configured using annotations only.
     *
     * @return ParamFetcherInterface
     */
    final protected function setMapForOrderByParam(ParamFetcherInterface $paramFetcher)
    {
        $orderByQueryParam = new QueryParam();
        $orderByQueryParam->map = true;
        $paramFetcher->addParam($orderByQueryParam);

        return $paramFetcher;
    }

    /**
     * @param $entity
     *
     * @return FOSRestView
     */
    final protected function serializeContext($entity, array $groups)
    {
        $context = new Context();
        $context->setGroups($groups);
        $context->enableMaxDepth();

        $view = FOSRestView::create($entity);
        $view->setContext($context);

        return $view;
    }
}
