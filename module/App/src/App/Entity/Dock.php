<?php

namespace App\Entity;

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
	* @ORM\Column(type="float")
	*/
	protected $latitude;

	/**
	* @ORM\Column(type="float")
	*/
	protected $longitude;

	/**
	* @ORM\Column(type="string", nullable=true)
	*/
	protected $location;
}