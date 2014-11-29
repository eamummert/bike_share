<?php

namespace Libs\Form;

use Doctrine\ORM\EntityManager;
use Libs\Entity\Manager\EntityManagerAwareInterface;
use Libs\Stdlib\Hydrator;
use Libs\Stdlib\Hydrator\HydratorProviderInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\Form as ZendForm;
use Zend\Stdlib\Hydrator as ZendHydrator;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * Extends the Zend Form to provide hydrator strategy building and lazy loading the iterator property.
 */
class Form extends ZendForm implements
	EntityManagerAwareInterface,
	HydratorAwareInterface
{
	/**
	 * @var EntityManager
	 */
	protected $entities;

	protected $hasAddedHydratorDefaults = false;

	protected $useHydratorDefaults = true;

	/**
	 * @param null|int|string|array $nameOrOptions Optional name for the element or an array of options
	 * @param array $options Optional options for the element
	 */
	public function __construct($nameOrOptions = null, $options = [])
	{
		if (is_array($nameOrOptions) && func_num_args() == 1)
		{
			if (isset($nameOrOptions['name']))
			{
				$name = $nameOrOptions['name'];
				unset($nameOrOptions['name']);
			}
			else
			{
				$name = null;
			}
			$options = $nameOrOptions;
		}
		else
		{
			$name = $nameOrOptions;
		}
		parent::__construct($name, $options);
	}

	/**
	 * Get the entity manager
	 *
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entities;
	}

	/**
	 * Set entity manager
	 *
	 * @param EntityManager $entities
	 * @return Form
	 */
	public function setEntityManager(EntityManager $entities)
	{
		$this->entities = $entities;
		return $this;
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

	public function extract()
	{
		$this->getHydrator();
		return parent::extract();
	}

	public function setHydrator(HydratorInterface $hydrator)
	{
		$this->hasAddedHydratorDefaults = false;
		return parent::setHydrator($hydrator);
	}

	/**
	 * Get the hydrator used when binding an object to the fieldset
	 *
	 * @return Hydrator\Entity
	 */
	public function getHydrator()
	{
		if (!$this->hydrator instanceof HydratorInterface)
		{
			$this->setHydrator(new Hydrator\Entity);
		}
		if (!$this->hasAddedHydratorDefaults && $this->useHydratorDefaults)
		{
			$this->attachHydratorDefaults($this->hydrator, $this);
			$this->hasAddedHydratorDefaults = true;
		}
		return $this->hydrator;
	}

	public function setUseHydratorDefaults($flag)
	{
		$this->useHydratorDefaults = (bool) $flag;
		return $this;
	}

	public function useHydratorDefaults()
	{
		return $this->useHydratorDefaults;
	}

	public function attachHydratorDefaults(HydratorInterface $hydrator, FieldsetInterface $fieldset)
	{
		if ($fieldset instanceof HydratorProviderInterface)
		{
			foreach ($fieldset->getHydratorSpecification() as $name => $spec)
			{
				if ($spec instanceof EntityManagerAwareInterface && $this->entities)
				{
					$spec->setEntityManager($this->entities);
				}
				$hydrator->addStrategy($name, $spec);
			}
		}

		foreach ($fieldset->getFieldsets() as $fieldset)
		{
			if (!$fieldset->getHydrator() instanceof HydratorInterface)
			{
				continue;
			}
			$this->attachHydratorDefaults($fieldset->getHydrator(), $fieldset);
		}
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

	/**
	 * Set the data (optional) and validate the form.
	 *
	 * Typically, will proxy to the composed input filter.
	 *
	 * @param  array|\ArrayAccess|\Traversable $data
	 * @return bool
	 * @throws \Zend\Form\Exception\DomainException
	 */
	public function isValid($data = [])
	{
		if (func_num_args() == 1)
		{
			$this->setData($data);
		}

		// We get the hydrator and force it to attach the default strategies
		$this->getHydrator();

		return parent::isValid();
	}

	public function __clone()
	{
		$this->getIterator();
		parent::__clone();
	}
}

