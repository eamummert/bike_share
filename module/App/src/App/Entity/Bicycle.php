<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Libs\Entity\AbstractEntity;

/**
* @ORM\Entity
*/
class Bicycle extends AbstractEntity
{
	/**
	* @ORM\Id
	* @ORM\GeneratedValue
	* @ORM\Column(type="integer")
	*/
	protected $id;

	/**
	* @ORM\OneToOne(targetEntity="Dock", inversedBy="bicycle")
	*/
	protected $dock;

	/**
	* @ORM\OneToMany(targetEntity="CheckOut", mappedBy="bicycle")
	*/
	protected $checkOuts;

	/**
	* @ORM\OneToMany(targetEntity="Gps", mappedBy="bicycle")
	*/
	protected $gpsData;

	public function __construct()
	{
		$this->checkOuts = new ArrayCollection;
		$this->gpsData = new ArrayCollection;
	}
}