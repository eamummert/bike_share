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
	* @ORM\Column(type="boolean")
	*/
	protected $locked = false;

	/**
	* @ORM\OneToMany(targetEntity="CheckOut", mappedBy="bicycle")
	* @ORM\OrderBy({"outTime" = "ASC"})
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

	public function unDock($checkout)
	{
		$this->dock->setBicycle(null);
		$this->dock->addCheckout($checkout);
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

	public function lock()
	{
		$this->locked = true;
	}

	public function unLock()
	{
		$this->locked = false;
	}
	public function switchLock()
	{
		$this->locked = !$this->locked;
	}
}
