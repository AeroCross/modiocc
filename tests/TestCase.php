<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    public function asAuthorizedUser(string $id = 'a')
    {
        $tokens = [
            'a' => env('TEST_PERSONAL_ACCESS_TOKEN_A', ''),
            'b' => env('TEST_PERSONAL_ACCESS_TOKEN_B', '')
        ];

        $headers = ['Authorization' => 'Bearer ' . $tokens[$id]];
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);

        return $this;
    }

    public function withoutAuthorization()
    {
        unset($this->defaultHeaders['Authorization']);
        return $this;
    }
}
