<?php

namespace Libs\Controller\Plugin;

class IsAllowed extends AbstractPlugin
{
	protected $zfcUserAuth;

	public function __construct($authService)
	{
		$this->zfcUserAuth = $authService;
	}
	
	public function __invoke($roles = [])
	{
		if (!is_array($roles))
		{
			$roles = [$roles];
		}

		$identity = $this->zfcUserAuth->getIdentity();
		if (!$identity)
		{
			return false;
		}
		
		if (in_array($identity->getState(), $roles))
		{
			return true;
		}

		return false;
	}
}

