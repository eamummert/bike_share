<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Libs\Entity\AbstractEntity;

/**
* @ORM\Entity
*/
class Fee extends AbstractEntity
{
	/**
	* @ORM\Id
	* @ORM\GeneratedValue
	* @ORM\Column(type="integer")
	*/
	protected $id;

	/**
	* @ORM\ManyToOne(targetEntity="Student", inversedBy="fee")
	*/
	protected $student;

	/**
	* @ORM\OneToOne(targetEntity="Checkout", mappedBy="fee")
	*/
	protected $checkout;

	/**
	* @ORM\Column(type="integer")
	*/
	protected $charge;

	/**
	* @ORM\Column(type="boolean")
	*/
	protected $paid = false;

	public function paidToString()
	{
		return $paid ? 'Paid' : 'Unpaid';
	}
}