<?php

namespace Tests\Actions;

use App\Actions\Authentication;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\BaseTestCase;

#[CoversClass(Authentication::class)]
class AuthenticationTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_a_registered_user_can_be_authenticated(): void
    {
        $this->post('/api/users', [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'Some random password',
        ]);

        $response = $this->post('/api/auth', [
            'email' => 'jane.doe@example.com',
            'password' => 'Some random password',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('token', (string) $response->getBody());
    }
}
