<?php

namespace Libs\Form;

use Libs\Stdlib\Hydrator;
use Zend\Form\Fieldset as ZendFieldset;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * Extends the Zend Fieldset to provide lazy loading for the iterator property.
 */
class Fieldset extends ZendFieldset
{
	public function allowObjectBinding($object)
	{
		return is_object($this->object) && is_object($object) && ($object instanceof $this->object || $this->object instanceof $object);
	}

	public function getHydrator()
	{
		if (!$this->hydrator instanceof HydratorInterface)
		{
			$this->setHydrator(new Hydrator\Entity);
		}
		return $this->hydrator;
	}

	public function count()
	{
		return $this->getIterator()->count();
	}

	public function getIterator()
	{
		if (!$this->iterator instanceof PriorityQueue)
		{
			$this->iterator = new PriorityQueue;
		}
		return $this->iterator;
	}

	public function add($elementOrFieldset, array $flags = [])
	{
		$this->getIterator();
		return parent::add($elementOrFieldset, $flags);
	}

	public function remove($elementOrFieldset)
	{
		$this->getIterator();
		return parent::remove($elementOrFieldset);
	}

	public function __clone()
	{
		$this->getIterator();
		parent::__clone();
	}
}

