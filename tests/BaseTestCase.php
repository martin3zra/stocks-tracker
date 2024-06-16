<?php

declare(strict_types=1);

namespace Tests;

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

class BaseTestCase extends TestCase
{

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
        array $headers = ['Accept'=> 'application/json'],
    ): Request {
        $uri = new Uri('', 'localhost', 80, $path);
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

    public function post(string $url): ResponseInterface
    {
        return $this->performRequest('POST', $url);
    }

    private function performRequest(string $method, string $url): ResponseInterface
    {
        $request = $this->createRequest($method, $url);

        // Act
        return $this->getAppInstance()->handle($request);
    }

}
