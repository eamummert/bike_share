<?php

namespace Libs\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Libs\Entity\Manager\ManagerManager;
use Libs\Entity\Manager\AliasResolverInterface;
use Libs\Entity\Repository\AbstractRepository;

/**
 * Fetch the entity manager or a repository for a given entity.
 */
class Entity extends AbstractPlugin
{
	/**
	 * @var ManagerManager
	 */
	protected $managers;

	/**
	 * Map of module names to entity manager names
	 *
	 * @var array
	 */
	protected $modules = [];

	/**
	 * @var AliasResolverInterface
	 */
	protected $resolver;

	/**
	 * @param ManagerManager $managers
	 * @param array $modules
	 * @param AliasResolverInterface $resolver
	 */
	public function __construct(ManagerManager $managers, array $modules = [], AliasResolverInterface $resolver)
	{
		$this->managers = $managers;
		$this->modules = $modules;
		$this->resolver = $resolver;
	}

	/**
	 * Get an entity manager or repository instance
	 *
	 * @param string $name Entity short name
	 *
	 * @throws Exception\RuntimeException if no entity manager is found
	 * @return AbstractRepository|EntityManager
	 */
	public function __invoke($name = null)
	{
		if (strpos($name, ':') !== false)
		{
			list($module) = explode(':', $name);
		}
		else
		{
			$module = $this->getModuleName();
		}
		/** @var EntityManager $entities */
		$entities = $this->managers->get($this->modules[$module]);

		if (func_num_args() == 0)
		{
			return $entities;
		}

		if (strpos($name, ':') === false)
		{
			$name = $this->resolver->resolve($entities, $name, [$this->getModuleName(), 'App']);
		}

		return $entities->getRepository($name);
	}
}

