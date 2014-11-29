<?php

namespace Libs\Controller;

use Zend\Mvc\Controller\ControllerManager as ZendControllerManager;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service locator for controllers.
 */
class ControllerManager extends ZendControllerManager
{
	public function addInitializer($initializer, $topOfStack = false)
	{
		parent::addInitializer($initializer, $topOfStack);
	}

	public function injectControllerDependencies($controller, ServiceLocatorInterface $controllers)
	{
		parent::injectControllerDependencies($controller, $controllers);

		if ($controller instanceof InjectApplicationEventInterface)
		{
			$services = $controllers->getServiceLocator();
			$event = $services->get('Application')->getMvcEvent();
			$controller->setEvent($event);
		}
	}
}

