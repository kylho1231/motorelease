<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test root url loads landing page and customer tracking portal is accessible.
     */
    public function test_the_application_loads_landing_page_and_tracking_portal(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MotoRelease');

        $trackResponse = $this->get(route('customer.track'));
        $trackResponse->assertStatus(200);
    }
}
