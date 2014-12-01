<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Libs\Entity\AbstractEntity;

/**
* @ORM\Entity
*/
class Student extends AbstractEntity
{
	/**
	* @ORM\Id
	* @ORM\GeneratedValue
	* @ORM\Column(type="integer")
	*/
	protected $id;

	/**
	* @ORM\OneToMany(targetEntity="CheckOut", mappedBy="student")
	*/
	protected $checkOuts;

	/**
	* @ORM\OneToMany(targetEntity="Fee", mappedBy="student")
	*/
	protected $fees;

	/**
	* @ORM\Column(type="string")
	*/
	protected $username;

	/**
	* This flags controls whether a person is allowed to checkout bicycles
	*
	* @ORM\Column(type="boolean")
	*/
	protected $allowed = true;	

	public function __construct()
	{
		$this->checkOuts = new ArrayCollection;
		$this->fees = new ArrayCollection;
	}

	public function addCheckout($checkout)
	{
		$this->checkOuts->add($checkout);
		$checkout->setStudent($this);
	}

	public function addFee($fee)
	{
		$this->fees->add($fee);
		$fee->setStudent($this);
	}
}