<?php

namespace Libs\Factory\Controller\Plugin;

use Libs\Controller\Plugin;
use RdnFactory\AbstractFactory;

class Auth extends AbstractFactory
{
	protected function create()
	{
		return new Plugin\Auth($this->service('zfcuser_auth_service'));
	}
}

