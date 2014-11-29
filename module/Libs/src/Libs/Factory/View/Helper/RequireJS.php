<?php

namespace Libs\Factory\View\Helper;

use Libs\Factory\AbstractFactory;
use Libs\View\Helper;

class RequireJS extends AbstractFactory
{
	protected function create()
	{
		$config = $this->config('libs', 'require_js');

		$helpers = $this->service('ViewHelperManager');
		$config['baseUrl'] = call_user_func($helpers->get('BasePath'), $config['baseUrl']);

		$library = $config['baseUrl'] .'/'. $config['library'];
		unset($config['library']);

		return new Helper\RequireJS($config, $library);
	}
}

