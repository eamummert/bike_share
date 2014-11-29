<?php

namespace Libs\View\Helper;

use Libs\View\Renderer\PhpRenderer;
use Zend\View\Helper\AbstractHelper as ZendAbstractHelper;

class AbstractHelper extends ZendAbstractHelper
{
	/**
	 * @var PhpRenderer
	 */
	protected $view;

	/**
	 * @inheritdoc
	 *
	 * @return null|PhpRenderer
	 */
	public function getView()
	{
		return parent::getView();
	}
}

