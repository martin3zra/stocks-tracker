<?php

declare(strict_types=1);

namespace Tests\Actions;

use App\Actions\StockFetcher;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\BaseTestCase;

#[CoversClass(StockFetcher::class)]
class StockFetcherTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_a_guest_user_cannot_request_stock(): void
    {
        $response = $this->get('/api/stock?q=aapl.us');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_a_authenticated_user_cannot_request_stock_with_missing_code(): void
    {
        $this->asAuthenticated();

        $response = $this->get('/api/stock');

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_a_authenticated_user_cannot_request_stock_with_unknow_code(): void
    {
        $this->asAuthenticated();

        $response = $this->get('/api/stock?q=aaxadw');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_a_authenticated_user_can_request_a_given_stock_code(): void
    {
        $this->asAuthenticated();

        $response = $this->get('/api/stock?q=aapl.us');

        $this->assertEquals(200, $response->getStatusCode());
        $decoded = json_decode((string) $response->getBody(), true);

        $this->assertEquals('APPLE', $decoded['name']);
        $this->assertEquals('AAPL.US', $decoded['symbol']);
    }
}
