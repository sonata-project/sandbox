<?php
/*
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Entity;

use AppBundle\Entity\Commerce\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product__product")
 */
class Travel extends Product
{
    /**
     * @ORM\Column(type="integer", name="travellers")
     *
     * @var int
     */
    protected $travellers;

    /**
     * @ORM\Column(type="datetime", name="travel_date")
     *
     * @var \DateTime
     */
    protected $travelDate;

    /**
     * @ORM\Column(type="integer", name="travel_days")
     *
     * @var int
     */
    protected $travelDays;

    /**
     * Sets travellers number.
     *
     *
     * @param int $travellers
     */
    public function setTravellers($travellers)
    {
        $this->travellers = $travellers;
    }

    /**
     * Returns travellers number.
     *
     * @return int
     */
    public function getTravellers()
    {
        return $this->travellers;
    }

    /**
     * Sets travel date.
     *
     *
     * @param \DateTime $travelDate
     */
    public function setTravelDate($travelDate)
    {
        $this->travelDate = $travelDate;
    }

    /**
     * Returns travel date.
     *
     * @return \DateTime
     */
    public function getTravelDate()
    {
        return $this->travelDate;
    }

    /**
     * Sets travel days number.
     *
     *
     * @param int $travelDays
     */
    public function setTravelDays($travelDays)
    {
        $this->travelDays = $travelDays;
    }

    /**
     * Returns travel days number.
     *
     * @return int
     */
    public function getTravelDays()
    {
        return $this->travelDays;
    }
}
