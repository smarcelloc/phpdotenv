<?php

namespace Dotenv\Exceptions;

use RuntimeException;

/**
 * Exception when unable to read the file.
 */
class FileNotReadException extends RuntimeException implements ExceptionInterface
{
}
