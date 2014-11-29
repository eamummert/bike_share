<?php

namespace Libs\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Inject the requireJS library before the view gets rendered.
 */
class RequireJS extends AbstractListener
{
	public function attach(EventManagerInterface $events)
	{
		$this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'onRender']);
	}

	public function onRender(MvcEvent $event)
	{
		$application = $event->getApplication();
		$services = $application->getServiceManager();

		$result = $event->getResult();
		if (!$result instanceof ViewModel || $result->terminate())
		{
			return;
		}

		$view = $services->get('ViewRenderer');
		$view->requireJS();
	}
}

