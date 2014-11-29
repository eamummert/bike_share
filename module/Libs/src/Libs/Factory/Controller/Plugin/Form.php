<?php

namespace Libs\Factory\Controller\Plugin;

use Libs\Controller\Plugin;
use RdnFactory\AbstractFactory;

class Form extends AbstractFactory
{
	protected function create()
	{
		return new Plugin\Form($this->service('FormElementManager'));
	}
}

