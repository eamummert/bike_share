<?php

namespace Libs\Factory\Listener;

use Libs\Listener;
use RdnFactory\AbstractFactory;

class InjectTemplate extends AbstractFactory
{
	protected function create()
	{
		return new Listener\InjectTemplate($this->service('ModuleManager'));
	}
}

