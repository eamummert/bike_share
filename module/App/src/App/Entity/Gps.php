<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Libs\Entity\AbstractEntity;

/**
* @ORM\Entity
*/
class Gps extends AbstractEntity
{
	/**
	* @ORM\Id
	* @ORM\GeneratedValue
	* @ORM\Column(type="integer")
	*/
	protected $id;

	/**
	* @ORM\ManyToOne(targetEntity="Bicycle", inversedBy="gpsData")
	*/
	protected $bicycle;

	/**
	* @ORM\ManyToOne(targetEntity="Checkout", inversedBy="gpsData")
	*/
	protected $checkout;

	/**
	* @ORM\Column(type="float")
	*/
	protected $latitude;

	/**
	* @ORM\Column(type="float")
	*/
	protected $longitude;

	/**
	* @ORM\Column(type="datetime")
	*/
	protected $time;

	public function __construct()
	{
		$this->time = new DateTime;
	}
}