<?php

namespace Libs\Stdlib\Hydrator;

use Doctrine\ORM\EntityRepository;
use Libs\Entity\EntityInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Doctrine entity hydrator.
 */
class Entity extends ClassMethods
{
	/**
	 * @var EntityRepository
	 */
	protected $repository;

	/**
	 * @var string[]
	 */
	protected $fields;

	/**
	 * @param EntityRepository $repository The entity repository
	 * @param string[] $fields The fields used to try to lookup the entity. defaults to array('id')
	 *
	 * @throws Exception\InvalidArgumentException
	 */
	public function __construct(EntityRepository $repository = null, array $fields = [])
	{
		if (func_num_args() > 0)
		{
			if (!$repository instanceof EntityRepository)
			{
				throw new \InvalidArgumentException(sprintf(
					'Repository must be an instance of EntityRepository, got %s instead'
					, gettype($repository)
				));
			}

			if (func_num_args() == 1)
			{
				$fields = ['id'];
			}

			$this->repository = $repository;
			$this->fields = $fields;
		}

		parent::__construct(false);
	}

	public function extract($object)
	{
		if ($object instanceof EntityInterface)
		{
			$data = $object->getData();
			$attr = [];
			foreach ($data as $key => $val)
			{
				$attr[$key] = $this->extractValue($key, $val);
			}
			return $attr;
		}
		else
		{
			return parent::extract($object);
		}
	}

	/**
	 * Hydrate an entity with given data. Optionally fetch existing entity if configured as such.
	 *
	 * @param array $data
	 * @param object $object
	 *
	 * @return object|EntityInterface
	 */
	public function hydrate(array $data, $object)
	{
		if (!is_null($this->repository) && !empty($this->fields))
		{
			$criteria = [];
			foreach ($this->fields as $prop)
			{
				$criteria[$prop] = $data[$prop];
			}
			$entity = $this->repository->findOneBy($criteria);
			if ($entity && ($object instanceof $entity || $entity instanceof $object))
			{
				$object = $entity;
			}
		}

		if ($object instanceof EntityInterface)
		{
			$oldData = $object->getData();
			foreach ($data as $key => $val)
			{
				$val = $this->hydrateValue($key, $val);
				if (array_key_exists($key, $oldData))
				{
					$object->setData([$key => $val]);
				}
				elseif (method_exists($object, $key))
				{
					$object->$key($val);
				}
			}
			return $object;
		}
		else
		{
			return parent::hydrate($data, $object);
		}
	}
}

