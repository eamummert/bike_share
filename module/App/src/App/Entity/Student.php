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

	public function __construct()
	{
		$this->checkOuts = new ArrayCollection;
		$this->fees = new ArrayCollection;
	}
}