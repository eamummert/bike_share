<?php

namespace App\Entity;

use DateTime;
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
	* @ORM\OneToOne(targetEntity="Fee", inversedBy="checkout", cascade={"persist"})
	*/
	protected $fee;

	/**
	* @ORM\Column(type="datetime")
	*/
	protected $outTime;

	/**
	* @ORM\ManyToOne(targetEntity="Dock", inversedBy="checkOuts")
	*/
	protected $outDock;

	/**
	* @ORM\Column(type="datetime", nullable=true)
	*/
	protected $inTime;

	/**
	* @ORM\ManyToOne(targetEntity="Dock", inversedBy="checkIns")
	*/
	protected $inDock;

	public function __construct()
	{
		$this->gpsData = new ArrayCollection;
		$this->outTime = new DateTime;
	}

	public function getOutTime($format = false)
	{
		if ($format)
		{
			return $this->outTime->format('d M Y, h:i:s A');
		}

		return $this->outTime;
	}

	public function getInTime($format = false)
	{
		if ($format)
		{
			return $this->inTime ? $this->inTime->format('d M Y, h:i:s A') : 'None';
		}

		return $this->inTime;
	}

	public function feeToString()
	{
		return $this->fee ? '$' . "{$this->fee->getCharge()}, {$this->fee->paidToString()}" : 'N/A';
	}

	public function assignFees()
	{
		if (!$this->inTime)
		{
			return false;
		}
		$diff = strtotime($this->getInTime(true)) - strtotime($this->getOutTime(true));
		if ($diff > 10)
		{
			$fee = new Fee;
			if ($diff > 30)
			{
				$fee->setCharge(5);
			}
			elseif ($diff > 20)
			{
				$fee->setCharge(3);
			}
			elseif ($diff > 10)
			{
				$fee->setCharge(1);
			}
			$this->setFee($fee);
			return $fee;
		}

		return false;
	}
}