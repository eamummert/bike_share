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

	public function unDock()
	{
		$this->dock->setBicycle(null);
		$this->dock = null;
	}

	public function addCheckOut($checkout)
	{
		$this->checkOuts->add($checkout);
		$checkout->setBicycle($this);
	}

	public function getCurrentCheckout()
	{
		foreach ($this->checkOuts as $co)
		{
			if ($co->getInTime() === null)
			{
				return $co;
			}
		}

		return null;
	}

	public function setDock($dock)
	{
		$this->dock = $dock;
		$dock->setBicycle($this);
	}
}