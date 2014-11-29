<?php

namespace Libs\Factory\Controller\Plugin;

use Libs\Controller\Plugin;
use RdnFactory\AbstractFactory;

class Entity extends AbstractFactory
{
	protected function create()
	{
		$managers = $this->service('EntityManagerManager');
		$modules = $this->config('entity_managers', 'modules');
		$resolver = $this->service('EntityManagerAliasResolver');
		return new Plugin\Entity($managers, $modules, $resolver);
	}
}

