<?php

namespace Libs\Controller\Plugin;

class Auth extends AbstractPlugin
{
	protected $auth;

	public function __construct($authService)
	{
		$this->auth = $authService;
	}

	public function __invoke()
	{
		return $this->auth;
	}
}
