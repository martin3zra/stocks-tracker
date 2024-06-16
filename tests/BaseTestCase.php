<?php

declare(strict_types=1);

namespace Tests;

use App\Models\QueryHistory;
use App\Models\User;
use DI\ContainerBuilder;
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

    protected array $headers = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->app = $this->getAppInstance();

        // Clean the database
        QueryHistory::query()->forceDelete();
        User::query()->forceDelete();

        $this->headers = [
            'Accept'=> 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function withHeaders(array $headers = []): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
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

        $query = '';
        if (str_contains($path, '?')) {
            $components = explode('?', $path);
            $path = $components[0];
            $query = $components[1];
        }

        $uri = new Uri('', 'localhost', 80, $path, $query);
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
        return $this->performRequest('POST', $url, [], $payload);
    }

    private function performRequest(
        string $method,
        string $url,
        array $headers = [],
        array $payload = [],
    ): ResponseInterface
    {
        $request = $this->createRequest($method, $url, array_merge($this->headers, $headers));

        if ($method == 'POST') {
            $request = $request->withParsedBody($payload);
        }

        return $this->app->handle($request);
    }

    public function asAuthenticated(array $user = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'Some random password',
        ]): void
    {
        $this->post('/api/users', $user);

        $response = $this->post('/api/auth', [
            'email' => $user['email'],
            'password' => $user['password'],
        ]);

        $data = json_decode((string)$response->getBody());

        $this->withHeaders(["Authorization" => "Bearer $data->token"]);
    }
}
