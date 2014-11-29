<?php

namespace Libs\View\Helper\RequireJS;

class ConfigScript
{
	protected $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function addDependencies($dependencies = [])
	{
		if (!is_array($dependencies))
		{
			$dependencies = [$dependencies];
		}

		$this->config['deps'] = array_merge_recursive($this->config['deps'], $dependencies);
	}

	/**
	 * @return array
	 */
	public function getDependencies()
	{
		return $this->config['deps'];
	}

	public function __toString()
	{
		$this->config['packages'] = array_unique($this->config['packages']);

		return 'var require = '. json_encode($this->config) .';';
	}
}

