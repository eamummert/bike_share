<?php

namespace Libs\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\View\Helper as ZendHelper;

/**
 * Extends the Zend helper to implement theme specific markup.
 */
class FormRow extends ZendHelper\FormRow
{
	protected $inputErrorClass = 'form-input-error Form-input--error';

	public function render(ElementInterface $element)
	{
		$helper = $this->getElementHelper();

		if ($element instanceof Csrf || $element instanceof Hidden)
		{
			return $helper->render($element);
		}

		$inputErrorClass = $this->getInputErrorClass();
		$hasErrors = count($element->getMessages()) > 0;

		if ($hasErrors && !empty($inputErrorClass))
		{
			$classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
			$classAttributes = $classAttributes . $inputErrorClass;

			$element->setAttribute('class', $classAttributes);
		}

		$markup = sprintf(
			'<div class="form-row Form-row%s">%s%s</div>'
			, $hasErrors ? ' Form-row--error' : ''
			, $this->renderLabel($element)
			, $this->renderInput($element)
		);

		return $markup;
	}

	public function renderLabel(ElementInterface $element)
	{
		$escaper = $this->getEscapeHtmlHelper();
		$helper = $this->getLabelHelper();
		$label = $element->getLabel();

		if (isset($label) && $label !== '')
		{
			if ($element->getOption('label_escape') !== false)
			{
				$label = $escaper($label);
			}
			if ($translator = $this->getTranslator())
			{
				$label = $translator->translate($label, $this->getTranslatorTextDomain());
			}
			$labelAttributes = $element->getLabelAttributes();

			if (empty($labelAttributes))
			{
				$labelAttributes = $this->labelAttributes;
			}

			$labelOpen  = $helper->openTag($labelAttributes);
			$labelClose = $helper->closeTag();
		}

		return sprintf(
			'<div class="form-cell form-label Form-label">%s%s</div>'
			, isset($label) && $label !== '' ? $labelOpen . $label . $labelClose : ''
			, $this->renderHint($element)
		);
	}

	public function renderInput(ElementInterface $element)
	{
		$helper = $this->getElementHelper();

		// todo pre/post text
		return sprintf(
			'<div class="form-cell form-input Form-input">%s%s%s%s%s</div>'
			, $element->getOption('before_input')
			, $helper->render($element)
			, $element->getOption('after_input')
			, $this->renderError($element)
			, $this->renderDescription($element)
		);
	}

	public function renderDescription(ElementInterface $element)
	{
		if ($desc = $element->getOption('description'))
		{
			return sprintf('<div class="form-desc Form-desc"><p>%s</p></div>', $desc);
		}
		return '';
	}

	public function renderHint(ElementInterface $element)
	{
		if ($hint = $element->getOption('hint'))
		{
			return sprintf('<p class="form-hint Form-hint">%s</p>', $hint);
		}
		return '';
	}

	public function renderError(ElementInterface $element)
	{
		$helper = $this->getElementErrorsHelper();
		$markup = $helper->render($element);
		if ($this->renderErrors && $markup)
		{
			return sprintf('<div class="form-error Form-error">%s</div>', $markup);
		}
		return '';
	}
}

