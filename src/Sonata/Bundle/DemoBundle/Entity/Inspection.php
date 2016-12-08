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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\User\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_car_inspection")
 */
class Inspection
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Car", inversedBy="inspections")
     * @ORM\JoinColumn(nullable=false)
     **/
    protected $car;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User\User", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     **/
    protected $inspector;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     */
    protected $date;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    protected $comment;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    protected $status;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Car $car
     */
    public function setCar(Car $car)
    {
        $this->car = $car;
    }

    /**
     * @return Car
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * @param $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getInspector()
    {
        return $this->inspector;
    }

    /**
     * @param mixed $inspector
     */
    public function setInspector(User $inspector = null)
    {
        $this->inspector = $inspector;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDate() ? $this->getDate()->format('Y-m-d') : 'n/a';
    }
}
