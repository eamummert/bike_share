<?php

namespace Libs\Controller\Plugin;

use Libs\Controller\AbstractController;
use Libs\Controller\ControllerManager;

/**
 * Fetch a controller from the service locator.
 */
class Controller extends AbstractPlugin
{
	/**
	 * @var ControllerManager
	 */
	protected $controllers;

	public function __construct(ControllerManager $controllers)
	{
		$this->controllers = $controllers;
	}

	/**
	 * Get a controller instance
	 *
	 * @param $name
	 *
	 * @return AbstractController
	 */
	public function __invoke($name)
	{
		if (strpos($name, ':') === false)
		{
			$module = $this->getModuleName();
			$name = $module .':'. $name;
		}
		return $this->controllers->get($name);
	}
}

