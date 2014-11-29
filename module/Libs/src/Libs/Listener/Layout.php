<?php

namespace Libs\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Listen for dispatched controllers and if a custom layout is configured for them.
 */
class Layout extends AbstractListener
{
	public function attach(EventManagerInterface $events)
	{
		$sharedEvents = $events->getSharedManager();
		$id = 'Zend\Stdlib\DispatchableInterface';
		$this->sharedListeners[$id][] = $sharedEvents->attach($id, MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 10);
	}

	public function onDispatch(MvcEvent $event)
	{
		/* @var $application \Zend\Mvc\Application */
		$application = $event->getApplication();
		$config = $application->getConfig();
		$config = $config['view_manager'];
		$controller = $event->getTarget();

		$mName = strstr(get_class($controller), '\\', true);
		$cName = $mName .':'. basename(str_replace('\\', '/', get_class($controller)));
		if (isset($config['layouts'][$cName]))
		{
			$controller->layout('layout/music');
		}
		elseif (isset($config['layouts'][$mName]))
		{
			$controller->layout($config['layouts'][$mName]);
		}
	}
}

