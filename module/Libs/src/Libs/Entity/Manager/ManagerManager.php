<?php

namespace Libs\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Exception\InvalidPluginException;
use Zend\ServiceManager\AbstractPluginManager;

class ManagerManager extends AbstractPluginManager
{
	public function validatePlugin($plugin)
	{
		if ($plugin instanceof EntityManager)
		{
			return true;
		}

        throw new InvalidPluginException(sprintf(
            'Entity Manager of type %s is invalid; must be Doctrine\ORM\EntityManager',
            is_object($plugin) ? get_class($plugin) : gettype($plugin)
        ));
	}
}


