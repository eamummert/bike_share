<?php

namespace Libs\Listener;

use Libs\Auth\RolesAuthorizedInterface;
use Libs\Controller\AbstractController;
use Libs\Exception;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ArrayUtils;

/**
 * Listen for dispatched controllers that require authentication or authorization.
 */
class AuthGuard extends AbstractListener
{
	protected $message = 'You do not have access to this area (%s).';

	public function attach(EventManagerInterface $events)
	{
		$shared = $events->getSharedManager();
		$id = 'Zend\Stdlib\DispatchableInterface';
		//$this->sharedListeners[$id][] = $shared->attach($id, MvcEvent::EVENT_DISPATCH, array($this, 'authenticate'), 9000);
		$this->sharedListeners[$id][] = $shared->attach($id, MvcEvent::EVENT_DISPATCH, [$this, 'authorize'], 9000);
	}

	public function authenticate(MvcEvent $event)
	{
		$controller = $event->getTarget();
		if (!($controller instanceof AbstractController))
		{
			return;
		}
	}

	public function authorize(MvcEvent $event)
	{
		$controller = $event->getTarget();
		$implementsInterface = $controller instanceof RolesAuthorizedInterface;
		if (!($implementsInterface && $controller instanceof AbstractController))
		{
			// We continue only if the controller implements the correct interface.
			// We also check for our abstract controller so the rest of the method
			// has code-completion.
			return;
		}

		$action = $event->getRouteMatch()->getParam('action');
		$params = $this->parseAllowedParams($controller, $action);
		if ($params === false)
		{
			return;
		}

		$key = $controller instanceof RolesAuthorizedInterface
			? 'role'
			: 'permission';
		foreach ($params as $index => $param)
		{
			if (!is_scalar($param) && (!is_array($param) || !isset($param[$key])))
			{
				throw new Exception\InvalidArgumentException(sprintf(
					"The access rule at index %s must be a scalar or an array containing the '%s' key, got %s instead from %s::%s()"
					, $index
					, $key
					, gettype($param)
					, get_class($controller)
					, $controller instanceof RolesAuthorizedInterface
						? 'getAllowedRoles'
						: 'getAllowedPermissions'
				));
			}
		}

		$identity = $controller->auth();
		if (!$identity->hasIdentity())
		{
			return $controller->redirect()->toRoute('zfcuser/login', [], [
				'query' => [
					'redirect' => $_SERVER['REDIRECT_URL'],
				],
			]);
		}

		// Update last access for this user.
		$user = $identity->getIdentity();
		$user->setLastAccess(new \DateTime());
		$controller->entity()->merge($user);
		$controller->entity()->flush();

		$flag = false;
		foreach ($params as $param)
		{
			if (is_scalar($param))
			{
				$param = [
					$key => $param,
				];
			}
			$flag = $controller->isAllowed($param);
			if ($flag !== false)
			{
				break;
			}
		}

		if ($flag === false)
		{
			throw new Exception\AccessDeniedException(sprintf($this->message, $identity->getIdentity()->getEmail()));
		}
	}

	/**
	 * Normalize the result of the getAllowedRoles() method only for the given action.
	 *
	 * The result is false if the controller does not provide any role constraints for
	 * the given action. Otherwise an array of acceptable roles is returned.
	 *
	 * @param RolesAuthorizedInterface|PermissionsAuthorizedInterface $controller
	 * @param string $action action name
	 *
	 * @return array|bool
	 * @throws Exception\InvalidArgumentException
	 */
	protected function parseAllowedParams($controller, $action)
	{
		$params = $controller instanceof RolesAuthorizedInterface
			? $controller->getAllowedRoles()
			: $controller->getAllowedPermissions();
		$method = $controller instanceof RolesAuthorizedInterface
			? 'getAllowedRoles'
			: 'getAllowedPermissions';
		$key = $controller instanceof RolesAuthorizedInterface
			? 'roles'
			: 'permissions';

		if (!is_array($params))
		{
			throw new Exception\InvalidArgumentException(sprintf(
				'%s::%s() must return an array, got %s instead'
				, get_class($controller)
				, $method
				, gettype($params)
			));
		}

		$simple = true;
		foreach ($params as $role)
		{
			if (is_array($role) && isset($role[$key]))
			{
				$simple = false;
				break;
			}
		}
		if ($simple)
		{
			return $params;
		}

		$guardAction = false;
		$rules = $params;
		$params = [];
		foreach ($rules as $index => $rule)
		{
			if (!isset($rule[$key]))
			{
				throw new Exception\InvalidArgumentException(sprintf(
					"The allow rule is missing the '%s' key at index %s in %s::%s()"
					, $key
					, $index
					, get_class($controller)
					, $method
				));
			}
			elseif (!is_array($rule[$key]))
			{
				throw new Exception\InvalidArgumentException(sprintf(
					"The '%s' key in an allow rule must be an array, got %s instead at index %s in %s::%s()"
					, $key
					, gettype($rule[$key])
					, $index
					, get_class($controller)
					, $method
				));
			}

			if (isset($rule['actions']) && !is_array($rule['actions']))
			{
				throw new Exception\InvalidArgumentException(sprintf(
					"The 'actions' key in an allow rule must be an array, got %s instead at index %s in %s::%s()"
					, gettype($rule['actions'])
					, $index
					, get_class($controller)
					, $method
				));
			}

			if (!isset($rule['actions']) || in_array($action, $rule['actions']))
			{
				$guardAction = true;
				$params = ArrayUtils::merge($params, $rule[$key]);
			}
		}

		return $guardAction ? $params : false;
	}
}

