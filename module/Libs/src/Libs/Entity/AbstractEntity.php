<?php

namespace Libs\Entity;

use ArrayAccess;
use BadMethodCallException;
use Doctrine\ORM\PersistentCollection;
use DomainException;
use InvalidArgumentException;
use LogicException;
use Traversable;

abstract class AbstractEntity implements ArrayAccess, EntityInterface
{
	/**
	 * @see ArrayAccess::offsetExists()
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return property_exists($this, $offset);
	}

	/**
	 * @see ArrayAccess::offsetGet()
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		$method = 'get'. ucfirst($offset);
		return $this->{$method}();
	}

	/**
	 * @see ArrayAccess::offsetSet()
	 * @return AbstractEntity
	 */
	public function offsetSet($offset, $value)
	{
		$method = 'set'. ucfirst($offset);
		return $this->{$method}($value);
	}

	/**
	 * @see ArrayAccess::offsetUnset()
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		throw new LogicException('Not allowed to unset an entity property');
	}

	/**
	 * Get all properties of this entity in an array.
	 *
	 * @return array
	 * @throws DomainException if entity has a data property
	 */
	public function getData()
	{
		$vars = get_object_vars($this);
		foreach ($vars as $key => $val)
		{
			if ($key == 'Data')
			{
				throw new DomainException(sprintf("'%s' cannot have property 'data'", get_class($this)));
			}
			else
			{
				$vars[$key] = $this->offsetGet($key);
			}
		}
		return $vars;
	}

	/**
	 * Set multiple properties of the entity at once.
	 *
	 * @param array|Traversable $data
	 *
	 * @return AbstractEntity
	 * @throws InvalidArgumentException if an array or a Traversable not provided
	 * @throws DomainException if trying to set data property
	 */
	public function setData($data = [])
	{
		if (!(is_array($data) || !$data instanceof Traversable))
		{
			throw new InvalidArgumentException("Expected array or Traversable got '". gettype($data) ."'");
		}
		foreach ($data as $key => $val)
		{
			if ($key == 'Data')
			{
				throw new DomainException(sprintf("'%s' cannot have property 'data'", get_class($this)));
			}
			elseif ($val instanceof PersistentCollection)
			{
				continue;
			}
			else
			{
				$this->offsetSet($key, $val);
			}
		}
		return $this;
	}

	/**
	 * Creates access to getter and setter magic methods for properties of entity
	 *
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		$action = substr($method, 0, 3);
		$property = substr($method, 3);
		$offset = lcfirst($property);
		if ($action == 'get')
		{
			if ($this->offsetExists($offset))
			{
				return $this->{$offset};
			}
			else
			{
				throw new InvalidArgumentException(sprintf("'%s' does not have property '%s'", get_class($this), $offset));
			}
		}
		elseif ($action == 'set')
		{
			if ($this->offsetExists($offset))
			{
				return $this->{$offset} = $arguments[0];
			}
			else
			{
				throw new InvalidArgumentException(sprintf("'%s' does not have property '%s'", get_class($this), $offset));
			}
		}
		else
		{
			throw new BadMethodCallException(sprintf("Call to undefined method %s::%s()", get_class($this), $method));
		}
	}
}

