<?php

namespace Lotos\Router;

use Psr\Http\Message\ServerRequestInterface;
use Lotos\Http\StrategyInterface;

class Group extends Router
{

    public function __construct(
        ServerRequestInterface $request,
        StrategyInterface $strategy,
        RouteCollectionInterface $collection
    ) {
        parent::__construct($request, $strategy, $collection);
    }

    public function addPrefix(string $prefix) : void
    {
        $this->prefix = '/'. trim($prefix, '/') .'/';
    }

    public function setGroupPort(RouteCollectionInterface $collection, int $port) : void
    {
        if($collection->count() > 0) {
            $collection->map(function($route) use ($port) {
                $route->setPort($port);
            });
        }
    }

    public function setGroupScheme(RouteCollectionInterface $collection, string $scheme) : void
    {
        if($collection->count() > 0) {
            $collection->map(function($route) use ($scheme) {
                $route->setScheme($scheme);
            });
        }
    }

    public function setGroupStrategy(RouteCollectionInterface $collection, StrategyInterface $strategy) : void
    {
        if($collection->count() > 0) {
            $collection->map(function($route) use ($strategy) {
                $route->setStrategy($strategy);
            });
        }
    }
}
