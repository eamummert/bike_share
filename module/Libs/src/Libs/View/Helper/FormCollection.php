<?php

namespace Libs\View\Helper;

use Zend\Form\Element\Collection;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper as ZendHelper;

/**
 * Extends the Zend helper to implement theme specific markup.
 */
class FormCollection extends ZendHelper\FormCollection
{
	public function render(ElementInterface $element)
	{
		$markup           = '';
		$templateMarkup   = '';
		$escapeHtmlHelper = $this->getEscapeHtmlHelper();
		$elementHelper    = $this->getElementHelper();
		$fieldsetHelper   = $this->getFieldsetHelper();
		$layout           = $element->getOption('layout');

		if ($element instanceof Collection && $element->shouldCreateTemplate())
		{
			$templateMarkup = $this->renderTemplate($element);
		}

		foreach ($element as $elementOrFieldset)
		{
			if ($elementOrFieldset instanceof FieldsetInterface)
			{
				$markup .= $fieldsetHelper($elementOrFieldset);
			}
			elseif ($elementOrFieldset instanceof ElementInterface)
			{
				$markup .= $elementHelper->render($elementOrFieldset);
			}
		}

		// If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
		if (!empty($templateMarkup))
		{
			$markup .= $templateMarkup;
		}

		if (($desc = $element->getOption('description')))
		{
			$markup = '<p>'. $desc .'</p>'. $markup;
		}

		// Every collection is wrapped by a fieldset if needed
		if ($this->shouldWrap)
		{
			// label attributes, hint?
			$label = $escapeHtmlHelper($element->getLabel());

			$fmt = <<<'HTML'
<fieldset class="form-row Form-row">
	<legend class="form-cell form-label Form-label %4$s">%2$s</legend>
	<div class="%1$s">%3$s</div>
</fieldset>
HTML;

			$class = 'form-cell form-input Form-input';
			if ($layout)
			{
				$class = ' Form--'. $layout;
			}
			$markup = sprintf($fmt, $class, $label, $markup, $element->getOption('label_class') ?: '');
		}

		return $markup;
	}
}

