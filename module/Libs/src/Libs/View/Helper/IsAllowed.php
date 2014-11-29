<?php

namespace Libs\View\Helper;

use Libs\Auth\Roles;
use Zend\Form\View\Helper as ZendHelper;

class IsAllowed extends ZendHelper\Form
{
	protected $authService;

	public function __construct($authService)
	{
		$this->authService = $authService;
	}

	public function __invoke($roles = [Roles::ROLE_ADMIN])
	{
		if (!is_array($roles))
		{
			$roles = [$roles];	
		}

		$identity = $this->authService->getIdentity();
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

