<?php

namespace Libs\Listener;

use Libs\Auth\Roles;
use Libs\Auth\RolesAuthorizedInterface;
use Libs\Controller\AbstractController;
use Libs\Exception;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ArrayUtils;

/**
 * Listen for dispatched controllers that require authentication or authorization.
 */
class AdminToolbar extends AbstractListener
{
	public function attach(EventManagerInterface $events)
	{
		$shared = $events->getSharedManager();
		$id = 'Zend\Stdlib\DispatchableInterface';
		$this->sharedListeners[$id][] = $shared->attach($id, MvcEvent::EVENT_DISPATCH, [$this, 'authorize'], 9000);
	}

	public function authorize(MvcEvent $event)
	{
		$controller = $event->getTarget();
		$identity = $controller->auth();
		if (!$controller->isAllowed(Roles::ROLE_ADMIN))
		{
			$application = $event->getApplication();
            $eventManager = $application->getEventManager();
			$sm = $application->getServiceManager();
            $sharedEventManager = $eventManager->getSharedManager();

            $eventManager->detachAggregate($sm->get('ZendDeveloperTools\FlushListener'));
            $eventManager->detachAggregate($sm->get('ZendDeveloperTools\ProfilerListener'));

            $sharedEventManager->clearListeners('profiler');
		}
	}
}

