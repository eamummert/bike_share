<?php

namespace Libs\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Abstract listener implementing the common detach process.
 */
abstract class AbstractListener implements ListenerAggregateInterface
{
	protected $listeners = [];

	protected $sharedListeners = [];

	abstract public function attach(EventManagerInterface $events);

	public function detach(EventManagerInterface $events)
	{
		foreach ($this->listeners as $index => $listener)
		{
			if ($events->detach($listener))
			{
				unset($this->listeners[$index]);
			}
		}

		$shared = $events->getSharedManager();
		foreach ($this->sharedListeners as $id => $listeners)
		{
			foreach ($listeners as $index => $listener)
			{
				if ($shared->detach($id, $listener))
				{
					unset($this->sharedListeners[$id][$index]);
				}
			}
		}
	}
}

