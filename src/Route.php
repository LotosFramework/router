<?php

namespace Lotos\Router;

use Lotos\Http\StrategyInterface;

class Route
{

    use StrategyTrait;

    private $prefix;
    private $path;
    private $method;
    private $handler = ['class'=>'', 'method'=>''];
    private $port;
    private $scheme;
    private $host;
    private $strategy;

    public function __construct($path, $handler)
    {
        $this->path = $path;
        [$this->handler['class'], $this->handler['method']] = explode('::', $handler);
    }

    public function setPrefix(string $prefix = null) : void
    {
        $this->prefix = $prefix;
        $this->path = str_replace($prefix, null, $this->path);
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function getHandler() : array
    {
        return $this->handler;
    }

    public function getHandlerClass() : string
    {
        return $this->handler['class'];
    }

    public function getHandlerMethod() : string
    {
        return $this->handler['method'];
    }

    public function setMethod(string $method) : void
    {
        $this->method = $method;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getUri() : string
    {
        return $this->prefix . $this->path;
    }

    public function setPort(int $port) : void
    {
        $this->port = $port;
    }

    public function getPort() : ?int
    {
        return $this->port;
    }

    public function getPrefix() : ?string
    {
        return $this->prefix;
    }

    public function getScheme() : ?string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme) : void
    {
        $this->scheme = $scheme;
    }

    public function getHost() : ?string
    {
        return $this->host;
    }

    public function setHost(string $host) : void
    {
        $this->host = $host;
    }

    public function getStrategy() : ?StrategyInterface
    {
        return $this->strategy;
    }

    public function setStrategy(StrategyInterface $strategy) : void
    {
        $this->strategy = $strategy;
    }

    public function getSize() : ?int
    {
        $uri = explode('/', $this->getUri());
        return count($uri);
    }
}
