<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="test_car")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "renault" = "Renault",
 *     "citroen" = "Citroen",
 *     "peugeot" = "Peugeot"
 * })
 */
abstract class Car
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
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Engine", cascade={"persist"}, fetch="EAGER")
     */
    protected $engine;

    /**
     * @ORM\ManyToOne(targetEntity="Engine", cascade={"persist"}, fetch="EAGER")
     */
    protected $rescueEngine;

    /**
     * @ORM\ManyToOne(targetEntity="Color", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="color_r", referencedColumnName="r"),
     *      @ORM\JoinColumn(name="color_g", referencedColumnName="g"),
     *      @ORM\JoinColumn(name="color_b", referencedColumnName="b"),
     *      @ORM\JoinColumn(name="color_material_id", referencedColumnName="material_id")
     * })
     */
    protected $color;

    /**
     * @ORM\OneToMany(targetEntity="Inspection", cascade={"persist", "remove"}, orphanRemoval=True, mappedBy="car")
     * @Assert\Valid
     */
    protected $inspections;

    /**
     * @var \AppBundle\Entity\Media\Media
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Media\Media", cascade={"persist"}, fetch="LAZY")
     */
    protected $media;

    public function __construct()
    {
        $this->inspections = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Engine $engine
     */
    public function setEngine(Engine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return Engine
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Engine $rescueEngine
     */
    public function setRescueEngine(Engine $rescueEngine)
    {
        $this->rescueEngine = $rescueEngine;
    }

    /**
     * @return Engine
     */
    public function getRescueEngine()
    {
        return $this->rescueEngine;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param Inspection[] $inspections
     */
    public function setInspections($inspections)
    {
        $this->inspections = new ArrayCollection();

        foreach ($inspections as $inspection) {
            $this->addInspection($inspection);
        }
    }

    /**
     * @return Inspection[]
     */
    public function getInspections()
    {
        return $this->inspections;
    }

    /**
     * @param Inspection $inspection
     */
    public function addInspection(Inspection $inspection)
    {
        $inspection->setCar($this);

        $this->inspections->add($inspection);
    }

    /**
     * @param Inspection $inspection
     */
    public function removeInspection(Inspection $inspection = null)
    {
        $this->inspections->removeElement($inspection);
    }

    /**
     * @return \AppBundle\Entity\Media\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param \AppBundle\Entity\Media\Media $media
     */
    public function setMedia(MediaInterface $media = null)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }
}
