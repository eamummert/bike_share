<?php

namespace Libs\Entity;

use App\Entity as AppEntity;
use Doctrine\ORM\Mapping as ORM;
use Libs\Auth\Roles;
use ZfcUser\Entity\UserInterface;

/**
*@ORM\Entity
*@ORM\Table(name="user")
*/
class User extends AbstractEntity implements UserInterface
{
	/**
	*@ORM\Id
	*@ORM\GeneratedValue
	*@ORM\Column(type="integer")
	*/
	protected $id;

	/**
	*@ORM\Column(type="string")
	*/
	protected $username;

	/**
	*@ORM\Column(type="string")
	*/
	protected $email;

	/**
	*@ORM\Column(type="string", nullable=true)
	*/
	protected $displayName;

	/**
	*@ORM\Column(type="string")
	*/
	protected $password;

	/**
	*@ORM\Column(type="integer")
	*/
	protected $state;

	/**
	*@ORM\Column(type="datetime")
	*/
	protected $lastAccess;

	public function __construct()
	{
		$this->lastAccess = new \DateTime();
		$this->creationDate = $this->lastAccess;
	}

	/**
	* Getters and Setters
	*/

    public function getId()
	{
		return $this->id;
	}

    public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

    public function getUsername()
	{
		return $this->username;
	}

    public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

    public function getEmail()
	{
		return $this->email;
	}

    public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

    public function getDisplayName()
	{
		return $this->displayName;
	}

    public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
		return $this;
	}

    public function getPassword()
	{
		return $this->password;
	}

    public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

    public function getState()
	{
		return $this->state;
	}

    public function setState($state)
	{
		$this->state = $state;
		return $this;
	}

	public function getLastAccess()
	{
		return $this->lastAccess;
	}

	public function setLastAccess($lastAccess)
	{
		$this->lastAccess = $lastAccess;
	}
	
	public function getCreationDate()
	{
		return $this->creationDate;
	}

	public function setCreationDate($creationDate)
	{
		$this->creationDate = $creationDate;
	}

	public function setPasswordVerify() {}
	public function setSubmit() {}
}
