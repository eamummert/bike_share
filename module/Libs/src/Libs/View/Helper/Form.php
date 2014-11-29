<?php

namespace Libs\View\Helper;

use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper as ZendHelper;

/**
 * Extends the Zend helper to implement theme specific markup.
 */
class Form extends ZendHelper\Form
{
	public function __invoke(FormInterface $form = null)
	{
		if (func_num_args() == 0)
		{
			return $this;
		}
		return $this->render($form);
	}

	public function render(FormInterface $form)
	{
		if (method_exists($form, 'prepare'))
		{
			$form->prepare();
		}

		$formContent = '';

		foreach ($form as $element)
		{
			if ($element instanceof FieldsetInterface)
			{
				$formContent .= $this->view->formCollection($element);
			}
			else
			{
				$formContent .= $this->view->formRow($element);
			}
		}

		$layout = $form->getOption('layout') ?: 'table';
		$class = 'form';
		$class .= ' Form--'. $layout;

		return sprintf(
			'%s
				<div class="%s">
					%s
				</div>
			%s',
			$this->openTag($form),
			$class,
			$formContent,
			$this->closeTag()
		);
	}
}

