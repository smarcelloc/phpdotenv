<?php

namespace Dotenv\Test;

use Dotenv\Env;
use Dotenv\Exceptions\FileNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * Only test loading the dotenv file to import in variable $_ENV.
 */
class EnvLoadTest extends TestCase
{
    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $_ENV = [];
    }

    /**
     * Test if the load method is located inside a directory and loading 
     * the dotenv file to the global variable $_ENV.
     *
     * @return void
     */
    public function test_load_directory_contains_file_env()
    {
        Env::load(__DIR__ . '/mocks');
        $this->assertSame('local', $_ENV['APP_ENV']);
    }

    /**
     * Test if the method load is clearing the $_ENV variable.
     *
     * @return void
     */
    public function test_load_clearing_variable_env()
    {
        $_ENV = ['NAME' => 'John Joy', 'EMAIL' => 'john@mail.com'];
        Env::load(__DIR__ . '/mocks');

        $this->assertArrayNotHasKey('NAME', $_ENV);
        $this->assertArrayNotHasKey('EMAIL', $_ENV);
    }

    /**
     * Test if is loading file dotenv not load variables invalid.
     *
     * @return void
     */
    public function test_load_dotenv_with_variables_invalid()
    {
        Env::load(__DIR__ . '/mocks');

        $this->assertArrayNotHasKey('LOG_CHANNEL', $_ENV);
        $this->assertArrayNotHasKey('LOG_DEPRECATIONS_CHANNEL', $_ENV);
    }

    /**
     * Test if the load method is located in a custom dotenv file.
     *
     * @return void
     */
    public function test_load_file_env()
    {
        Env::load(__DIR__ . '/mocks/.env.testing');
        $this->assertSame('testing', $_ENV['APP_ENV']);
    }

    /**
     * Test if loading file dotenv empty.
     *
     * @return void
     */
    public function test_load_file_env_empty()
    {
        $_ENV = ['NAME' => 'John Joy', 'EMAIL' => 'john@mail.com'];

        Env::load(__DIR__ . '/mocks/.env.empty');
        $this->assertSame([], $_ENV);
    }

    /**
     * Test if the load method returns an exception when not finding the dotenv file.
     *
     * @return void
     */
    public function test_failure_when_not_found_file_env()
    {
        $filename = __DIR__ . '.env.custom';
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage("This file was not found '{$filename}'");

        Env::load($filename);
    }

    /**
     * Test if the load method returns an exception when not finding the dotenv file
     * contains in directory.
     *
     * @return void
     */
    public function test_failure_when_not_found_file_env_contains_in_directory()
    {
        $directory = __DIR__;
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage("This file was not found '{$directory}/.env'");

        Env::load($directory);
    }
}
