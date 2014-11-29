<?php

namespace Libs\Exception;

/**
 * Exceptions with message viewable to the public.
 *
 * Exceptions of this type, when thrown, will instruct the error handler to
 * display the actual exception message instead of the generic message.
 */
class PublicException extends RuntimeException
{

}

