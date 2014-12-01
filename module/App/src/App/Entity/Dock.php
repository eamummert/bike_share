<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Libs\Entity\AbstractEntity;

/**
* @ORM\Entity
*/
class Dock extends AbstractEntity
{
	/**
	* @ORM\Id
	* @ORM\GeneratedValue
	* @ORM\Column(type="integer")
	*/
	protected $id;

	/**
	* @ORM\OneToOne(targetEntity="Bicycle", inversedBy="dock")
	*/
	protected $bicycle;

	/**
	* @ORM\OneToMany(targetEntity="CheckOut", mappedBy="outDock")
	*/
	protected $checkOuts;

	/**
	* @ORM\OneToMany(targetEntity="CheckOut", mappedBy="inDock")
	*/
	protected $checkIns;

	/**
	* @ORM\Column(type="boolean")
	*/
	protected $locked = false;

	/**
	* @ORM\Column(type="float")
	*/
	protected $latitude;

	/**
	* @ORM\Column(type="float")
	*/
	protected $longitude;

	/**
	* Name of location. Ex. Carver Hall, Memorial Union South, etc.
	*
	* @ORM\Column(type="string", nullable=true)
	*/
	protected $name;

	public function __construct()
	{
		$this->checkOuts = new ArrayCollection;
		$this->checkIns = new ArrayCollection;
	}

	public function addCheckout($checkout)
	{
		$this->checkOuts->add($checkout);
		$checkout->setOutDock($this);
	}

	public function addCheckin($checkin)
	{
		$this->checkIns->add($checkin);
		$checkin->setInDock($this);
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

	public function isLocked()
	{
		return $this->locked;
	}
}
