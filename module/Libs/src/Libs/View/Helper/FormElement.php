<?php

namespace Libs\View\Helper;

use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper as ZendHelper;
use Zend\Stdlib\CallbackHandler;

/**
 * Extends Zend's FormElement with ability to add custom helpers
 * to form elements via FormElement::addViewHelper
 */
class FormElement extends ZendHelper\FormElement
{
	/**
	 * Array of extra view helpers
	 *
	 * @var CallbackHandler[]
	 */
	protected $customHelpers = [];

	/**
	 * Registers a view helper to a form element.
	 *
	 * By default the helper is used when the element being tested is an instance
	 * of the given element. A callback can be used instead of a name for more
	 * complex checks.
	 *
	 * todo priority queue
	 *
	 * @param string|callable $nameOrCallback Element FQCN. Ex: "Zend\Form\Element\Textarea" or callback to test if an element should use this helper
	 * @param string $helper Form helper to use. Ex: "form_ckeditor"
	 *
	 * @return CallbackHandler
	 * @throws Exception\RuntimeException
	 */
	public function addViewHelper($nameOrCallback, $helper)
	{
		if (is_string($nameOrCallback))
		{
			$callback = function($element) use ($nameOrCallback)
			{
				return $element instanceof $nameOrCallback;
			};
		}
		elseif (is_callable($nameOrCallback))
		{
			$callback = $nameOrCallback;
		}
		else
		{
			throw new \RuntimeException('Must provide a string or valid callback to register custom view helper');
		}
		$handler = new CallbackHandler($callback, ['helper' => $helper]);
		$this->customHelpers[] = $handler;
		return $handler;
	}

	/**
	 * Unregister a custom view helper.
	 *
	 * @param CallbackHandler $handler Callback handler returned from addViewHelper
	 *
	 * @return FormElement
	 */
	public function removeViewHelper(CallbackHandler $handler)
	{
		foreach ($this->customHelpers as $key => $helper)
		{
			if ($helper === $handler)
			{
				unset($this->customHelpers[$key]);
			}
		}
		return $this;
	}

	public function render(ElementInterface $element)
	{
		$renderer = $this->getView();
		if (!method_exists($renderer, 'plugin'))
		{
			// Bail early if renderer is not pluggable
			return '';
		}

		foreach ($this->customHelpers as $handler)
		{
			if (call_user_func($handler->getCallback(), $element))
			{
				$helper = $renderer->plugin($handler->getMetadatum('helper'));
				return $helper($element);
			}
		}

		// No custom view helpers match this element, defer to default render function
		return parent::render($element);
	}
}

