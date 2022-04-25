<?php

namespace Dotenv\Exceptions;

use RuntimeException;

/**
 * Exception for files not found.
 */
class FileNotFoundException extends RuntimeException implements ExceptionInterface
{
}
