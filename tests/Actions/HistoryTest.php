<?php

namespace Tests\Actions;

use App\Actions\History;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\BaseTestCase;

#[CoversClass(History::class)]
class HistoryTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_a_guest_user_cannot_see_the_stock_history(): void
    {
        $response = $this->get('/api/history');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_a_authenticated_user_can_see_the_searches_of_stock_history(): void
    {
        // Acting as an authenticated user
        $this->asAuthenticated();

        // Perform a search for the stock code `aapl.us`
        $this->get('/api/stock?q=aapl.us');

        // Perform a request to see the history of all the searches
        $response = $this->get('/api/history');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode((string) $response->getBody(), true);

        $this->assertEquals('APPLE', $decoded[0]['name']);
        $this->assertEquals('AAPL.US', $decoded[0]['symbol']);
    }
}
