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
        $pattern = '/' . $patternKey . '=' . $patternValue . '/mu';

        $envVariables = [];

        if (preg_match_all($pattern, $content, $matches)) {
            $keys = array_map('trim', $matches[1]);
            $values = array_map('self::transformToAppropriateValue', $matches[2]);
            $envVariables = array_combine($keys, $values);
        }

        return self::mapLinkEnv($envVariables);
    }

    /**
     * All variables in dotenv files are strings, so some reserved values were 
     * created to allow you to return an appropriate value type.
     *
     * @param string $valueEnv
     * @return string|bool|null
     */
    protected static function transformToAppropriateValue(string $valueEnv)
    {
        $valueEnv = trim($valueEnv);

        switch (mb_strtolower($valueEnv)) {
            case 'true':
                return true;

            case 'false':
                return false;

            case 'null':
                return null;

            default:
                return $valueEnv;
        }
    }

    /**
     * Seek to link the environment variables. When variable value is 
     * "$NAME_VAR$", it will be linked with NAME_VAR variable.
     * 
     * @param array $envVariables
     * @return array
     */
    protected static function mapLinkEnv(array $envVariables)
    {
        if (count($envVariables) <= 1) {
            return $envVariables;
        }

        foreach ($envVariables as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            $value = self::getLinkEnvValue($envVariables, $value);
            $envVariables[$key] = self::sanitizeEnvValue($value);
        }

        return $envVariables;
    }

    /**
     * @param array $envVariables
     * @param string $value
     * @return string|bool|null
     */
    private static function getLinkEnvValue(array $envVariables, string $value)
    {
        if (mb_ereg('"(.*)\${(\w+)}(.*)"', $value, $matches)) {
            if (!isset($envVariables[$matches[2]])) {
                return $value;
            }

            $valueOtherVariable = $envVariables[$matches[2]] ?? $value;

            if ($matches[1] || $matches[3]) {
                return strval($matches[1]) . strval($valueOtherVariable) . strval($matches[3]);
            }

            return $valueOtherVariable;
        }

        return $value;
    }

    /**
     * @param string|bool|null $value
     * @return string|bool|null
     */
    private static function sanitizeEnvValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (mb_ereg('^"(.*)"$', $value, $matches)) {
            $value = $matches[1];
        }

        return mb_ereg_replace('\\\\"', '"', $value);
    }
}
