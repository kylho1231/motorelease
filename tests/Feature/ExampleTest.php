<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test root url redirects to customer tracking portal.
     */
    public function test_the_application_redirects_to_customer_tracking(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('customer.track'));

        $trackResponse = $this->get(route('customer.track'));
        $trackResponse->assertStatus(200);
    }
}
