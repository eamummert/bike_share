<?php

namespace Libs\Factory\Controller\Plugin;

use Libs\Controller\Plugin;
use RdnFactory\AbstractFactory;

class IsAllowed extends AbstractFactory
{
	protected function create()
	{
		return new Plugin\IsAllowed($this->service('zfcuser_auth_service'));
	}
}

