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

namespace Sonata\Bundle\DemoBundle\Controller;

use Sonata\ProductBundle\Controller\BaseProductController;

/**
 * Overwrite methods from the BaseProductController if you want to change the behavior
 * for the current product.
 */
class GoodieController extends BaseProductController
{
}
