<?php

namespace Tests\Actions;

use App\Actions\UserRegister;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\BaseTestCase;

#[CoversClass(UserRegister::class)]
class UserRegisterTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_a_guest_can_create_a_user_account(): void
    {
        $response = $this->post('/api/users', [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'Some random password',
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_a_guest_cannot_create_a_user_account_with_invalid_email_address(): void
    {
        $response = $this->post('/api/users', [
            'name' => 'Jane Doe',
            'email' => 'jane.doe',
            'password' => 'Some random password',
        ]);

        $object = json_decode($response->getBody())[0];
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($object->message, "Invalid email");
    }

    public function test_a_guest_cannot_create_a_user_account_with_missing_information(): void
    {
        $response = $this->post('/api/users', [
            'name' => 'Jane Doe',
        ]);

        $this->assertEquals(422, $response->getStatusCode());
    }
}
