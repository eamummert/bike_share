<?php

namespace Libs\Module;

use ReflectionClass;
use Symfony\Component\Yaml\Yaml;
use Zend\ModuleManager\Feature;
use Zend\Stdlib\ArrayUtils;

abstract class AbstractModule implements
	Feature\AutoloaderProviderInterface,
	Feature\ConfigProviderInterface
{
	protected $config;

	protected $name;

	protected $path;

	protected $rootPath;

	/**
	 * Get autoloader configuration for this module.
	 *
	 * @return array
	 */
	public function getAutoloaderConfig()
	{
		return [
			'Zend\Loader\StandardAutoloader' => [
				'namespaces' => [
					$this->getName() => $this->getPath(),
				],
			],
		];
	}

	/**
	 * Get configuration for this module.
	 *
	 * @return array|\Traversable
	 */
	public function getConfig()
	{
		if ($this->config === null)
		{
			$this->config = include dirname(dirname(dirname(__DIR__))) .'/config/module.base.config.php';
			$env = (defined('APPLICATION_ENV') ? APPLICATION_ENV : 'production');
			$pattern = $this->getRootPath() .'/config/{module,'. $env .',local}.config.php';
			foreach (glob($pattern, GLOB_BRACE) as $path)
			{
				$this->config = ArrayUtils::merge($this->config, include $path);
			}
		}
		return $this->config;
	}

	/**
	 * Get this module's name.
	 *
	 * @return string
	 */
	public function getName()
	{
		if ($this->name === null)
		{
			$ref = new ReflectionClass($this);
			$this->name = $ref->getNamespaceName();
		}
		return $this->name;
	}

	/**
	 * Get the path to this module's src/ directory.
	 *
	 * @return string
	 */
	public function getPath()
	{
		if ($this->path === null)
		{
			$ref = new ReflectionClass($this);
			$this->path = dirname($ref->getFileName());
		}
		return $this->path;
	}

	/**
	 * Get the path to this module's root directory
	 *
	 * @return string
	 */
	public function getRootPath()
	{
		if ($this->rootPath === null)
		{
			$path = $this->getPath();
			for ($i = 0, $l = count(explode('\\', $this->getName())); $i < $l; $i++)
			{
				$path = dirname($path);
			}
			$this->rootPath = dirname($path);
		}
		return $this->rootPath;
	}
}
