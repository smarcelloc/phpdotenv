<?php

namespace Dotenv\Test;

use Dotenv\Env;
use PHPUnit\Framework\TestCase;

class EnvLoadTest extends TestCase
{
    public function test_index()
    {
        $this->assertTrue(Env::index());
    }
}
