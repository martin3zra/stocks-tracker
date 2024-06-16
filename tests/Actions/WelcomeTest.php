<?php

declare(strict_types=1);

namespace Tests\Actions;

use App\Actions\Welcome;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\BaseTestCase;

#[CoversClass(Welcome::class)]
class WelcomeTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }


    public function test_can_see_welcome_message(): void
    {
        // Arrange
        $response = $this->get('/api');

        // Act
        $body = (string) $response->getBody();

        $expected = ['message' => 'Welcome to Stock tracker API'];

        // Assert
        $this->assertEquals($expected, json_decode($body, true));
    }
}
