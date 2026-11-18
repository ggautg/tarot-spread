<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * La portada debe responder aunque todavía no haya tiradas sembradas.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertOk();
    }
}
