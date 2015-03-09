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

use AppBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product__product")
 */
class Travel extends Product
{
    /**
     * @var integer
     */
    protected $travellers;

    /**
     * @var \DateTime
     */
    protected $travelDate;

    /**
     * @var integer
     */
    protected $travelDays;

    /**
     * Sets travellers number
     *
     * @ORM\Column(type="integer", name="travellers")
     * @param int $travellers
     */
    public function setTravellers($travellers)
    {
        $this->travellers = $travellers;
    }

    /**
     * Returns travellers number
     *
     * @return int
     */
    public function getTravellers()
    {
        return $this->travellers;
    }

    /**
     * Sets travel date
     *
     * @ORM\Column(type="DateTime", name="travel_date")
     * @param \DateTime $travelDate
     */
    public function setTravelDate($travelDate)
    {
        $this->travelDate = $travelDate;
    }

    /**
     * Returns travel date
     *
     * @return \DateTime
     */
    public function getTravelDate()
    {
        return $this->travelDate;
    }

    /**
     * Sets travel days number
     *
     * @ORM\Column(type="integer", name="travel_days")
     * @param int $travelDays
     */
    public function setTravelDays($travelDays)
    {
        $this->travelDays = $travelDays;
    }

    /**
     * Returns travel days number
     *
     * @return int
     */
    public function getTravelDays()
    {
        return $this->travelDays;
    }
}
