<?php

namespace Libs\Exception;

/**
 * If user does not have access to the requested content. The message is public.
 */
class AccessDeniedException extends HttpException
{
	protected $code = 403;
}

