<?php

namespace App\Listener;

use Zend\EventManager\EventManagerInterface as Events;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\InjectTemplateListener;

class InjectTemplate extends InjectTemplateListener
{
	protected $modules;

	protected $mvcCapable = [];

	public function __construct(ModuleManager $modules)
	{
		$this->modules = $modules;
	}

	public function attach(Events $events)
	{
		$sharedEvents = $events->getSharedManager();
		$sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, [$this, 'injectTemplate'], -89);
	}

	protected function deriveModuleNamespace($controller)
	{
		$moduleName = strstr($controller, '\\', true);
		if ($moduleName === false)
		{
			return '';
		}

		if (!array_key_exists($moduleName, $this->mvcCapable))
		{
			$module = $this->modules->getModule($moduleName);
			$ref = new \ReflectionClass($module);
			$modulePath = dirname($ref->getFilename());
			
			$mvcDir = $modulePath .'/views/'. $this->inflectName($moduleName) .'/mvc';
			$this->mvcCapable[$moduleName] = is_dir($mvcDir);
		}

		return $this->mvcCapable[$moduleName] ? $moduleName .'/Mvc' : $moduleName;
	}
}

