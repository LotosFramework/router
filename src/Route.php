<?php

namespace Lotos\Router;

use Lotos\Http\StrategyInterface;
use Psr\Http\Message\RequestInterface;

class Route
{

    use StrategyTrait;

    private ?string $prefix = null;
    private ?string $path = null;
    private ?string $method = null;
    private array $handler = ['class'=>'', 'method'=>''];
    private ?int $port = null;
    private ?string $scheme = null;
    private ?string $host = null;
    private ?StrategyInterface $strategy = null;
    private array $vars = [];

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

    public function getMethod() : ?string
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

    private function addVar(string $name, string $value) : void
    {
        $this->vars[$name] = $value;
    }

    public function parseVars(RequestInterface $request) : void
    {
        $pathParts = explode('/', $this->prefix.$this->path);
        foreach($pathParts as $part) {
            if($this->isVar($part)) {
                $this->setVars($request, $pathParts);
                $this->updateHandlerClass();
            }
        }
    }

    private function updateHandlerClass() : void
    {
        $handler = array_map(function($item) {
            return array_key_exists($this->getVarName($item), $this->vars)
                ? $this->toCamelCase($this->vars[$this->getVarName($item)])
                : $item;
        }, explode('\\', $this->handler['class']));
        $this->handler['class'] = implode('\\', $handler);
    }

    private function toCamelCase(string $snaked) : string
    {
        if (substr_count($snaked, '_') > 0) {
            $arr = explode('_', $snaked);
            $arr = array_map(function($item)  {
                return ucfirst($item);
            }, $arr);
            $snaked = implode('', $arr);
        }
        return $snaked;
    }

    private function isVar($var) : bool {
        if((substr($var, 0, 1) == '{') && ((substr($var, -1) == '}') || (substr($var, -2) == '}?'))) {
            return true;
        }
        return false;
    }

    private function getVarName(string $var) : string
    {
        return str_replace(['{', '}', ':s', ':d', ':a'], '', $var);
    }

    private function setVars(RequestInterface $request, array $pathArr)
    {
        $requestArr = explode('/', $request->getUri()->getPath());
        $keys = array_diff($pathArr, $requestArr);
        $vals = array_diff($requestArr, $pathArr);
        foreach($keys as $index => $name) {
            $this->vars[$this->getVarName($name)] = (array_key_exists($index, $vals)) ? $vals[$index] : '';
        }
    }

    public function getVars() : ?array
    {
        return $this->vars;
    }
}
