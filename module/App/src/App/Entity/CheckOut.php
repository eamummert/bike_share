<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Libs\Entity\AbstractEntity;

/**
* @ORM\Entity
*/
class CheckOut extends AbstractEntity
{
	/**
	* @ORM\Id
	* @ORM\GeneratedValue
	* @ORM\Column(type="integer")
	*/
	protected $id;

	/**
	* @ORM\ManyToOne(targetEntity="Bicycle", inversedBy="checkOuts")
	*/
	protected $bicycle;

	/**
	* @ORM\OneToMany(targetEntity="Gps", mappedBy="checkout")
	*/
	protected $gpsData;

	/**
	* @ORM\ManyToOne(targetEntity="Student", inversedBy="checkOuts")
	*/
	protected $student;

	/**
	* @ORM\OneToOne(targetEntity="Fee", inversedBy="checkout")
	*/
	protected $fee;

	public function __construct()
	{
		$this->gpsData = new ArrayCollection;
	}
}