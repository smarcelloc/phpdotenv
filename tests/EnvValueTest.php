<?php

namespace Dotenv\Test;

use Dotenv\Env;
use PHPUnit\Framework\TestCase;

class EnvValueTest extends TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        Env::load(__DIR__ . '/mocks');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $_ENV = [];
    }

    /**
     * Tests if the environment variable return true (bool).
     *
     * @return void
     */
    public function test_env_for_return_value_true()
    {
        $this->assertTrue($_ENV['APP_DEBUG']);
    }

    /**
     * Tests if the environment variable return false (bool).
     *
     * @return void
     */
    public function test_env_for_return_value_false()
    {
        $this->assertFalse($_ENV['AWS_USE_PATH_STYLE_ENDPOINT']);
    }

    /**
     * Tests if the environment variable return null.
     *
     * @return void
     */
    public function test_env_for_return_value_null()
    {
        $this->assertNull($_ENV['REDIS_PASSWORD']);
    }

    /**
     * Tests if the environment variable return a string trim.
     *
     * @return void
     */
    public function test_env_for_return_value_string_trim()
    {
        $this->assertSame('mysql', $_ENV['DB_CONNECTION']);
    }

    /**
     * Tests if the environment variable return a string empty.
     *
     * @return void
     */
    public function test_env_for_return_value_string_empty()
    {
        $this->assertSame('', $_ENV['APP_KEY']);
    }

    /**
     * Tests if the environment variable was stripped to the two quotation 
     * marks at the beginning and end of the value.
     *
     * @return void
     */
    public function test_env_return_value_string_trim_stripped_two_quotation()
    {
        $this->assertSame('hello@example.com', $_ENV['MAIL_FROM_ADDRESS']);
    }

    /**
     * Test if the environment variable is linked to another variable.
     *
     * @return void
     */
    public function test_env_return_the_value_other_variable()
    {
        $this->assertSame($_ENV['PUSHER_APP_KEY'], $_ENV['MIX_PUSHER_APP_KEY']);
        $this->assertSame($_ENV['PUSHER_APP_CLUSTER'], $_ENV['MIX_PUSHER_APP_CLUSTER']);
        $this->assertSame($_ENV['APP_NAME'] . " is \"framework\"", $_ENV['MAIL_FROM_NAME']);
    }

    /**
     * Test if environment variable does not identify link
     * with another variable.
     *
     * @return void
     */
    public function test_load_return_the_value_other_variable_not_identical()
    {
        $this->assertSame('${NOT_EXISTS}', $_ENV['AWS_ACCESS_KEY_ID']);
    }
}
