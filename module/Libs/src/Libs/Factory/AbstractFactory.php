<?php

namespace Libs\Factory;

use Doctrine\ORM\EntityManager;
use Libs\Auth\Identity\IdentityInterface;
use Libs\Controller\AbstractController;
use Libs\Exception;
use Libs\Form\Fieldset;
use Libs\Form\Form;
use Libs\Upload;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Make it is easy to create factory classes. Contains helper methods for frequently used services.
 */
abstract class AbstractFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @return mixed
	 */
	abstract protected function create();

	protected $services;

	/**
	 * Overwrite the FactoryInterface method to enable internal helpers.
	 *
	 * @param ServiceLocatorInterface $services
	 *
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $services)
	{
		$this->setServiceLocator($services);
		return $this->create();
	}

	public function setServiceLocator(ServiceLocatorInterface $services)
	{
		if ($services instanceof ServiceLocatorAwareInterface)
		{
			$services = $services->getServiceLocator();
		}
		$this->services = $services;
	}

	public function getServiceLocator()
	{
		return $this->services;
	}

	/**
	 * Get configuration by key. Pass multiple arguments to grab nested items.
	 *
	 * <code>
	 * // grab the 'adapter' item inside the 'auth' array
	 * $config = $this->config('auth', 'adapter');
	 * </code>
	 *
	 * @param string $key Grab the value for this config key.
	 *
	 * @return mixed
	 * @throws Exception\RuntimeException
	 */
	public function config($key = null)
	{
		$config = $this->service('Config');
		$args = func_get_args();
		while (count($args))
		{
			if (!is_array($config))
			{
				throw new Exception\RuntimeException('Config item is not an array, trying to grab '. implode(' -> ', func_get_args()));
			}
			$key = array_shift($args);
			$config = isset($config[$key]) ? $config[$key] : [];
		}
		return $config;
	}

	/**
	 * Get a controller with the given name.
	 *
	 * @param string $name
	 *
	 * @return AbstractController
	 */
	public function controller($name)
	{
		return $this->service('ControllerManager')->get($this->prefixModule($name));
	}

	/**
	 * Get a database adapter with the given name.
	 *
	 * @param string $name
	 *
	 * @return Adapter
	 */
	public function database($name = 'default')
	{
		return $this->service('DbAdapterManager')->get($name);
	}

	/**
	 * Get an entity repository by the entity name and optionally the manager name.
	 *
	 * @param string $entity Entity name (ex: 'Entry', 'Module:Entry')
	 * @param string $manager Entity manager name (defaults to current module name)
	 *
	 * @return \Doctrine\ORM\EntityRepository
	 */
	public function entity($entity, $manager = null)
	{
		if (strpos($entity, ':') !== false && func_num_args() == 1)
		{
			list($module) = explode(':', $entity);
			$manager = $this->config('entity_managers', 'modules', $module);
		}
		$entities = $this->entities($manager);
		if (strpos($entity, ':') === false)
		{
			$resolver = $this->service('EntityManagerAliasResolver');
			$entity = $resolver->resolve($entities, $entity, [$this->getModuleName(), 'App']);
		}
		return $entities->getRepository($entity);
	}

	/**
	 * Get the entity manager with the given name.
	 *
	 * @param string $manager Entity manager name
	 *
	 * @return EntityManager
	 */
	public function entities($manager = null)
	{
		if ($manager === null)
		{
			$module = $this->getModuleName();
			$manager = $this->config('entity_managers', 'modules', $module);
		}
		return $this->service('EntityManagerManager')->get($manager);
	}

	/**
	 * Get a form, fieldset, or element with the given name.
	 *
	 * @param string $name
	 *
	 * @return Form|Fieldset|Element
	 */
	public function form($name)
	{
		return $this->service('FormElementManager')->get($this->prefixModule($name));
	}

	/**
	 * Get the current identity from the identity provider.
	 *
	 * @return IdentityInterface|null
	 */
	public function identity()
	{
		return $this->service('IdentityProvider')->getIdentity();
	}

	/**
	 * Fetch route parameter by the given name.
	 *
	 * @param string $name Parameter name to fetch.
	 * @param mixed $default Default value to use when parameter is missing.
	 *
	 * @return array|mixed|null
	 */
	public function params($name, $default = null)
	{
		$match = $this->service('Application')->getMvcEvent()->getRouteMatch();
		if (!$match instanceof RouteMatch)
		{
			return $default;
		}
		return $match->getParam($name, $default);
	}

	/**
	 * Get a service by the given name.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 * @throws Exception\RuntimeException if service locator not set
	 */
	public function service($name = 'ServiceManager')
	{
		if (!$this->services instanceof ServiceLocatorInterface)
		{
			throw new Exception\RuntimeException('No service locator set for factory. Set the service locator using the setServiceLocator() method first.');
		}
		return $this->services->get($name);
	}

	/**
	 * Get the upload container.
	 *
	 * @return Upload\ContainerInterface
	 */
	public function uploads()
	{
		return $this->service('UploadContainer');
	}

	/**
	 * Generate a URL based on a route
	 *
	 * @param string $route Route name
	 * @param array $params Route parameters
	 * @param array $options Route options. If boolean, and no fourth argument, used as $reuseMatchedParams.
	 * @param bool $reuseMatchedParams Whether to reuse matched parameters
	 *
	 * @return string
	 */
	public function url($route = null, $params = [], $options = [], $reuseMatchedParams = false)
	{
		return $this->controller('Webdev:Index')->url($route, $params, $options, $reuseMatchedParams);
	}

	/**
	 * Prefix a service name with the current module name, if one is not already set.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	public function prefixModule($name)
	{
		if (strpos($name, ':') === false)
		{
			$name = $this->getModuleName() .':'. $name;
		}
		return $name;
	}

	/**
	 * Get name of module current class belongs to.
	 *
	 * @return string
	 */
	public function getModuleName()
	{
		return strstr(get_class($this), '\\', true);
	}
}

