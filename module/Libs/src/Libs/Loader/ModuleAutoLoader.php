<?php

namespace Libs\Loader;

use SplFileInfo;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Loader\ModuleAutoloader as ZendModuleAutoloader;

/**
 * Module.php autoloader that follows the Webdev convention of module structure.
 */
class ModuleAutoloader extends ZendModuleAutoloader
{
	protected $inflector;

	protected function loadModuleFromDir($path, $class)
	{
		$module = substr($class, 0, -7);
		$path = substr($path, 0, -strlen($module));
		$dir = $this->inflectName($module);
		$file = new SplFileInfo($path . $dir . '/src/'. $module .'/Module.php');
		if ($file->isReadable() && $file->isFile())
		{
			require_once $file->getRealPath();
			if (class_exists($class))
			{
				$this->moduleClassMap[$class] = $file->getRealPath();
				return $class;
			}
		}
		return false;
	}

	protected function inflectName($name)
	{
		if ($this->inflector === null)
		{
			$this->inflector = new CamelCaseToDash;
		}
		return strtolower($this->inflector->filter($name));
	}
}

