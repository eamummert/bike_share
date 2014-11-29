<?php

namespace Libs\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AbstractController extends AbstractActionController
{
	/**
	 * Overwrite parent::__call and throw exceptions in case plugin is not callable but params are given.
	 *
	 * @param string $method
	 * @param array $params
	 *
	 * @return mixed
	 * @throws Exception\InvalidArgumentException
	 */
	public function __call($method, $params)
	{
		$plugin = $this->plugin($method);
		if (is_callable($plugin))
		{
			return call_user_func_array($plugin, $params);
		}
		elseif (!empty($params))
		{
			throw new Exception\InvalidArgumentException("The plugin '$method' is not directly callable, but parameters were provided. Did you instead mean to invoke a public method on the plugin with the given parameters?");
		}

		return $plugin;
	}
}
