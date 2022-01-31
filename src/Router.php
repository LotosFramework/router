<?php

namespace Lotos\Router;

use \Closure;
use Fig\Http\Message\{RequestMethodInterface, StatusCodeInterface};
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface,
    StreamFactoryInterface
};
use Lotos\Router\Exception\{
    RouteNotFoundException,
    MethodNotAllowedException,
};
use Psr\Container\ContainerInterface;
use Lotos\Http\Message\HttpMessagesTrait;
use Lotos\Http\StrategyInterface;

class Router implements RequestMethodInterface, StatusCodeInterface
{

    use HttpMethodsTrait;
    use StrategyTrait;
    use HttpMessagesTrait;

    const METHOD_ANY = 'ANY';

    private ?RouteCollectionInterface $routeCollection;
    private ?Route $route = null;

    protected $prefix;

    public function __construct(
        private ServerRequestInterface $request,
        private StrategyInterface $defaultStrategy,
        private ?RouteCollectionInterface $collection = null,
        private ?ErrorPagesInterface $errorPages = null
    ) {
        $this->serverRequest = $request;
        $this->routeCollection = $collection ?? new RouteCollection();
        $this->errorPages = $errorPages;
    }

    public function map(string $method, string $path, $handler) : self
    {
        $this->route = new Route($path, $handler);
        $this->route->setMethod($method);
        $this->route->setPrefix($this->prefix);
        $this->routeCollection->addRoute($this->route);
        return $this;
    }

    public function setScheme(string $scheme) : self
    {
        if($this instanceof Group) {
            $this->setGroupScheme($this->routeCollection
                ->where('prefix', $this->prefix)
                ->whereNull('scheme'),
                $scheme);
        } else {
            $this->route->setScheme($scheme);
        }
        return $this;
    }

    public function setHost(string $host) : self
    {
        if($this instanceof Group) {
            $this->setGroupHost($this->routeCollection
                ->where('prefix', $this->prefix)
                ->whereNull('host'),
                $host);
        } else {
            $this->route->setHost($host);
        }
        return $this;
    }

    public function middleware($handler) : self
    {
        return $this;
    }

    public function setPort(int $port) : self
    {
        if($this instanceof Group) {
            $this->setGroupPort($this->routeCollection
                ->where('prefix', $this->prefix)
                ->whereNull('port'),
                $port);
        } else {
            $this->route->setPort($port);
        }
        return $this;
    }

    public function group(string $prefix, Closure $handler) : Group
    {
        $group = new Group($this->serverRequest, $this->defaultStrategy, $this->routeCollection);
        $group->addPrefix($this->prefix.trim($prefix,'/'));
        $handler($group);
        return $group;
    }

    public function addRoutePattern(string $patternAlias, string $patternRule) : self
    {
        return $this;
    }

    public function resolve(ContainerInterface $container) : void
    {
        try {
            $errorPages = $this->errorPages ?? new ErrorPages($container->get(ResponseInterface::class));
            $this->getRouteByPath();
            $this->checkExistsRoute();
            $this->checkIsAllowedMethod();
            $this->dispatch($container);
        } catch(RouteNotFoundException $e) {
            $this->defaultStrategy->process($errorPages->notFound($this->serverRequest));
        } catch(MethodNotAllowedException $e) {
            $this->defaultStrategy->process($errorPages->notAllowed($this->serverRequest));
        }
    }

    public function dispatch(ContainerInterface $container) : void
    {
        $this->route->parseVars($this->serverRequest);
        $vars = $this->route->getVars();
        if($vars) {
            $this->serverRequest->getUri()->addVars($this->route->getVars());
        }
        $obj = $container->get($this->route->getHandlerClass());
        $method = $this->route->getHandlerMethod();
        $this->route->getStrategy()->process($obj->$method($this->serverRequest));
    }

    public function registerMiddleware(string $name, string $path)
    {

    }

    private function getRouteByPath() : void
    {
      $this->route = $this->routeCollection->getRouteByPath(
            $this->serverRequest->getMethod(),
            $this->serverRequest->getUri()->getPath()
        ) ?? $this->routeCollection->getRouteByUri(
                $this->serverRequest->getMethod(),
                $this->serverRequest->getUri()->getPath()
            );
    }

    private function checkIsAllowedMethod() : void
    {
        if($this->isAllowedMethod() === false) {
            throw new MethodNotAllowedException;
        }
    }

    private function isAllowedMethod() : bool
    {
        return (($this->route->getMethod() == $this->serverRequest->getMethod()) === true)
            ? true
            : ($this->route->getMethod() === self::METHOD_ANY);
    }

    private function checkExistsRoute() : void
    {
        if(is_null($this->route) === true) {
            throw new RouteNotFoundException();
        }
    }

}
