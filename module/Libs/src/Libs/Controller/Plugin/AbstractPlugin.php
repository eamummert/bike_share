<?php

namespace Libs\Controller\Plugin;

use Libs\Controller\AbstractController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin as ZendAbstractPlugin;

/**
 * Abstract plugin with helpful methods.
 */
class AbstractPlugin extends ZendAbstractPlugin
{
	/**
	 * @var AbstractController
	 */
	protected $controller;

	/**
	 * @inheritdoc
	 *
	 * @return null|AbstractController
	 */
	public function getController()
	{
		return parent::getController();
	}

	protected function getModuleName()
	{
		$controller = get_class($this->controller);
		return strstr($controller, '\\', true);
	}
}

