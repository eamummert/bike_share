<?php

namespace Libs\Auth;

/**
 * Restrict access to an object based on roles.
 */
interface RolesAuthorizedInterface
{
	/**
	 * Return a list of roles that are allowed access to this object.
	 *
	 * @return array
	 */
	public function getAllowedRoles();
}

