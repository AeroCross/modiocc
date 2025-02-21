<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function asAuthorizedUser()
    {
        $headers = ['Authorization' => 'Bearer ' . env('TEST_PERSONAL_ACCESS_TOKEN', 'create a PAT and add it here')];
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);

        return $this;
    }
}
