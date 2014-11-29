<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Libs;

use ReflectionClass;
use Symfony\Component\Yaml\Yaml;
use Zend\ModuleManager\Feature;
use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

		$application = $e->getApplication();
		$services = $application->getServiceManager();
		$events = $application->getEventManager();
		//$listeners = $services->get('ListenerManager');
		$config = $application->getConfig();

		/**
		 * Attach various event listeners
		 */
		uasort($config['libs']['listeners'], function($a, $b)
		{
			$a = is_numeric($a) ? $a : 1;
			$b = is_numeric($b) ? $b : 1;
			return $b - $a;
		});
		foreach ($config['libs']['listeners'] as $name => $priority)
		{
			if (is_numeric($name))
			{
				$name = $priority;
			}
			$events->attachAggregate($services->get($name));
		}
    }

    public function getConfig()
    {
		return array_merge_recursive(
			include __DIR__ . '/config/module.config.php',
			include __DIR__ . '/config/module.base.config.php'
		);
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
