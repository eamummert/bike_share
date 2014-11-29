<?php

namespace Libs\Factory\Controller\Plugin;

use Libs\Controller\Plugin;
use RdnFactory\AbstractFactory;

class Mpd extends AbstractFactory
{
	protected function create()
	{
		$options = $this->config('mpd_config');
		return new Plugin\Mpd($options);
	}
}

