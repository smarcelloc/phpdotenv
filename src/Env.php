<?php

namespace Dotenv;

use Dotenv\Exceptions\FileNotFoundException;
use Dotenv\Exceptions\FileNotReadException;

class Env
{
    /**
     * Load environment variables with dotenv file.
     *
     * @param string $filename
     * @throws FileNotFoundException
     * @throws FileNotReadException
     * @return void
     */
    public static function load(string $filename)
    {
        if (is_dir($filename)) {
            $filename .= '/.env';
        }

        if (!is_file($filename)) {
            throw new FileNotFoundException("This file was not found '{$filename}'");
        }

        $content = file_get_contents($filename);

        if ($content === false) {
            throw new FileNotReadException("Could not read this file '{$filename}'.");
        }

        $_ENV = self::parse($content);
    }

    /**
     * Parse the contents of the dotenv file to return an array 
     * containing environment variables.
     *
     * @param string $content
     * @return array
     */
    protected static function parse(string $content): array
    {
        $patternKey = '^(?:(?!\n)\s)*(\w*)(?:(?!\n)\s)*';
        $patternValue = '(?:(?!\n)\s)*("(?:[^"\\\\]|\\\\.|\n)*"|[^\s#]*)';
        $pattern = '/' . $patternKey . '=' . $patternValue . '/m';

        $envVariables = [];

        if (preg_match_all($pattern, $content, $matches)) {
            $keys = array_map('trim', $matches[1]);
            $values = array_map('trim', $matches[2]);
            $envVariables = array_combine($keys, $values);
        }

        return $envVariables;
    }
}
