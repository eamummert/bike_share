<?php

namespace Libs\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractAutoLoader implements AbstractFactoryInterface, MutableCreationOptionsInterface
{
	protected $namespace;

	protected $factories = [];

	protected $creationOptions;

	public function canCreateServiceWithName(ServiceLocatorInterface $services, $cName, $rName)
	{
		if (strpos($rName, ':') === false)
		{
			return false;
		}

		$factoryClassName = $this->inflectClassName($rName, 'Factory');
		if ($this->isValidService($factoryClassName))
		{
			return true;
		}

		$className = $this->inflectClassName($rName);
		return $this->isValidService($className);
	}

	public function createServiceWithName(ServiceLocatorInterface $services, $cName, $rName)
	{
		$options = $this->creationOptions;
		$this->creationOptions = null;
		$hasOptions = !is_null($options) && (!is_array($options) || !empty($options));

		$factoryClassName = $this->inflectClassName($rName, 'Factory');
		if ($this->isValidService($factoryClassName))
		{
			if (!isset($this->factories[$factoryClassName]))
			{
				$this->factories[$factoryClassName] = new $factoryClassName;
			}
			$factory = $this->factories[$factoryClassName];

			if (!$factory instanceof FactoryInterface)
			{
				throw new Exception\ServiceNotCreatedException(sprintf(
					'An invalid factory (%s) was registered for %s%s. Must implement %s.'
					, $cName
					, ($rName ? '(alias: ' . $rName . ')' : '')
					, $factoryClassName
					, 'Zend\ServiceManager\FactoryInterface'
				));
			}

			return $factory->createService($services);
		}

		return $this->createFromInvokable($rName, $hasOptions, $options);
	}

	public function inflectClassName($rName, $prefix = '')
	{
		list($module, $shortName) = explode(':', $rName, 2);
		return $module .'\\'. ($prefix ? $prefix .'\\' : '') . $this->namespace .'\\'. str_replace(':', '\\', $shortName);
	}

	protected  function isValidService($className)
	{
		if (!class_exists($className))
		{
			return false;
		}

		$ref = new \ReflectionClass($className);
		return $ref->isInstantiable();
	}

	public function setCreationOptions(array $options)
	{
		$this->creationOptions = $options;
	}

	protected function createFromInvokable($rName, $hasOptions, $options)
	{
		$className = $this->inflectClassName($rName);
		return $hasOptions ? new $className($options) : new $className;
	}
}

