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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="test_car")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"renault" = "Renault", "citroen" = "Citroen", "peugeot" = "Peugeot"})
 */
class Car
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
     * @ORM\OneToMany(targetEntity="Inspection", cascade={"persist", "remove"}, mappedBy="car")
     * @Assert\Valid
     */
    protected $inspections;

    public function __construct()
    {
        $this->inspections = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $createdAt
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @param Inspection[] $inspections
     */
    public function setInspections($inspections)
    {
        $this->inspections->clear();

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
     * @return void
     */
    public function addInspection(Inspection $inspection)
    {
        $inspection->setCar($this);
        $this->inspections->add($inspection);
    }

    /**
     * @param Inspection $inspection
     * @return void
     */
    public function removeInspection(Inspection $inspection)
    {
        $this->inspections->removeElement($inspection);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }
}
