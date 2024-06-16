<?php

declare(strict_types=1);

namespace Tests;

use App\Models\QueryHistory;
use App\Models\User;
use DI\ContainerBuilder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;
use Symfony\Component\Dotenv\Dotenv;
use \Illuminate\Database\Capsule\Manager as Capsule;

class BaseTestCase extends TestCase
{
    /**
     * @var \Slim\App
     */
    protected $app;

    public function setUp(): void
    {
        parent::setUp();

        $this->app = $this->getAppInstance();

        // Clean the database
        QueryHistory::query()->forceDelete();
        User::query()->forceDelete();
    }

    /**
     * @throws Exception
     */
    protected function getAppInstance(): \Slim\App
    {
        $dotEnv = new Dotenv();
        $dotEnv->load(__DIR__ . '/../.env');

        $builder = new ContainerBuilder();

        $services = require __DIR__ . '/../public/services.php';
        $services($builder);
        $container = $builder->build();

        AppFactory::setContainer($container);

        $app = AppFactory::create();

        require __DIR__ . '/../public/bootstrap.php';

        return $app;
    }

    /**
     * @throws Exception
     */
    protected function createRequest(
        string $method = 'GET',
        string $path = '/',
        array $headers = [],
    ): Request {

        $uri = new Uri('', 'localhost', 80, $path, '');
        $handle = fopen('php://temp', 'w+');

        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, [], [], $stream);
    }

    public function get(string $url): ResponseInterface
    {
        return $this->performRequest('GET', $url);
    }

    public function post(string $url, array $payload): ResponseInterface
    {
        return $this->performRequest('POST', $url, ['Content-Type' => 'application/json'], $payload);
    }

    private function performRequest(string $method, string $url, array $headers = [], array $payload = []): ResponseInterface
    {
        $defaultHeaders = ['Accept'=> 'application/json', 'Content-Type' => 'application/json'];
        $request = $this->createRequest($method, $url, array_merge($defaultHeaders, $headers));

        if ($method == 'POST') {
            $request = $request->withParsedBody($payload);
        }

        return $this->app->handle($request);
    }
}
