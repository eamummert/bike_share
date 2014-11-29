<?php

namespace Libs\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Exception\InvalidPluginException;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Service locator for event listeners.
 */
class ListenerManager extends AbstractPluginManager
{
	public function validatePlugin($plugin)
	{
		if ($plugin instanceof ListenerAggregateInterface)
		{
			return true;
		}

		throw new InvalidPluginException(sprintf(
			'Listener of type %s is invalid; must implement Zend\EventManager\ListenerAggregateInterface',
			is_object($plugin) ? get_class($plugin) : gettype($plugin),
			__NAMESPACE__
		));
	}
}

