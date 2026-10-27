<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('henrik.auth.allow_local_identity', true);
        config()->set('henrik.auth.local_email', 'informatica@donihue.cl');
        config()->set('henrik.auth.local_name', 'Oficina de Informática');
    }
}
