<?php

namespace Libs\Factory\View\Helper;

use Libs\View\Helper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IsAllowed implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $pluginManager)
	{
		/* @var $pluginManager HelperPluginManager */
        $serviceManager = $pluginManager->getServiceLocator();

        /* @var $authService AuthenticationService */
        $authService = $serviceManager->get('zfcuser_auth_service');

		return new Helper\IsAllowed($authService);
	}
}
