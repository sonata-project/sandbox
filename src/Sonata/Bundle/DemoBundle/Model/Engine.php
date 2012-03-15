<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Model;

class Engine
{
    protected $name;

    protected $power;

    public function  __construct($name = null, $power = null)
    {
        $this->name = $name;
        $this->power = $power;
    }

    /**
     * @param $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $power
     * @return void
     */
    public function setPower($power)
    {
        $this->power = $power;
    }

    /**
     * @return int
     */
    public function getPower()
    {
        return $this->power;
    }

    public function __toString()
    {
        return $this->getName();
    }
}

