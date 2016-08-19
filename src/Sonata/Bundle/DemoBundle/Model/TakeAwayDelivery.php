<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Model;

use Sonata\Component\Delivery\BaseServiceDelivery;

/**
 * Class TakeAwayDelivery.
 *
 * Custom delivery class example
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class TakeAwayDelivery extends BaseServiceDelivery
{
    /**
     * {@inheritdoc}
     */
    public function isAddressRequired()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'take_away';
    }
}
