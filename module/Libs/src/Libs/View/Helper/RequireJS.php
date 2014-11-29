<?php

namespace Libs\View\Helper;

use Libs\View\Helper\RequireJS\ConfigScript;
use Libs\View\Renderer\PhpRenderer;

class RequireJS extends AbstractHelper
{
	/**
	 * @var ConfigScript
	 */
	protected $config;

	/**
	 * @var string
	 */
	protected $library;

	/**
	 * @var bool
	 */
	protected $libraryIncluded = false;

	/**
	 * @var PhpRenderer
	 */
	protected $view;

	/**
	 * @param array $config RequireJS configuration (http://requirejs.org/docs/api.html#config)
	 * @param string $library Path to requireJS library
	 */
	public function __construct(array $config, $library)
	{
		$this->config = new ConfigScript($config);
		$this->library = $library;
	}

	/**
	 * Include the requireJS library and config object with dependencies.
	 *
	 * @param string|string[] $dependencies
	 */
	public function __invoke($dependencies = [])
	{
		$this->config->addDependencies($dependencies);

		if (!$this->libraryIncluded && count($this->config->getDependencies()))
		{
			$this->view->inlineScript()
				->appendScript($this->config)
				->appendFile($this->library)
			;
			$this->libraryIncluded = true;
		}
	}
}
