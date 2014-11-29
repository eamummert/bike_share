<?php

namespace Libs\Controller\Plugin;

use Libs\Form\Form as WebdevForm;
use Libs\Form\Fieldset;
use Zend\Form\Element;
use Zend\Form\FormElementManager;

/**
 * Fetch a form or the form service locator.
 */
class Form extends AbstractPlugin
{
	/**
	 * @var FormElementManager
	 */
	protected $forms;

	public function __construct(FormElementManager $forms)
	{
		$this->forms = $forms;
	}

	/**
	 * Get a form/element instance or the form service locator.
	 *
	 * @param string $name Form/element name
	 * @param mixed $options (optional) constructor parameters
	 *
	 * @return WebdevForm|Fieldset|Element|FormElementManager
	 */
	public function __invoke($name = null, $options = null)
	{
		if (func_num_args() == 0)
		{
			return $this->forms;
		}
		if (strpos($name, ':') === false)
		{
			$module = $this->getModuleName();
			$name = $module .':'. $name;
		}
		return $this->forms->get($name, $options);
	}
}

