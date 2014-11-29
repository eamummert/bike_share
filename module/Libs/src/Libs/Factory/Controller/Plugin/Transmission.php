<?php

namespace Libs\Factory\Controller\Plugin;

use Libs\Controller\Plugin;
use RdnFactory\AbstractFactory;

class Transmission extends AbstractFactory
{
	protected function create()
	{
		$options = $this->config('transmission');
		return new Plugin\Transmission($options);
	}
}

