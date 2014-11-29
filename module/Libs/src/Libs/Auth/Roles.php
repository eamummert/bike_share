<?php 

namespace Libs\Auth;

class Roles
{
	const ROLE_BANNED = 0;
	const ROLE_GUEST = 1;
	const ROLE_VIEW = 2;
	const ROLE_ADMIN = 3;

	public static function roleToString($role)
	{
		switch ($role)
		{
			case self::ROLE_BANNED:
				return 'Banned';
			case self::ROLE_GUEST:
				return 'Guest';
			case self::ROLE_VIEW:
				return 'View Access';
			case self::ROLE_ADMIN:
				return 'Admin';
			default:
				return 'Invalid';
		}
	}

	public static function getRoles()
	{
		return [
			self::ROLE_BANNED => 'Banned',
			self::ROLE_GUEST => 'Guest',
			self::ROLE_VIEW => 'View Access',
			self::ROLE_ADMIN => 'Admin',
		];
	}
}
