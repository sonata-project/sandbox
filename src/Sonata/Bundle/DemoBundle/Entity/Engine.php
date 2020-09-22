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

namespace Sonata\Bundle\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_car_engine")
 */
class Engine
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", length=100)
     */
    protected $power;

    /**
     * @var \App\Entity\Media\Media
     * @ORM\ManyToOne(targetEntity="App\Entity\Media\Media", cascade={"persist"}, fetch="LAZY")
     */
    protected $media;

    /**
     * @param string $name
     * @param string $power
     */
    public function __construct($name = null, $power = null)
    {
        $this->name = $name;
        $this->power = $power;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name
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

    public function setMedia(MediaInterface $media)
    {
        $this->media = $media;
    }

    /**
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }
}
