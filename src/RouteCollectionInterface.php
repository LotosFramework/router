<?php

namespace Lotos\Router;

use Ds\Sequence as CollecionInterface;

interface RouteCollectionInterface extends CollectionInterface
{
    public function addRoute(Route $route) : void;
    public function getRouteByPath(string $method, string $path) : ?Route;
    public function getRouteByUri(string $method, string $uri) : ?Route;
    public function getRoutesBySize(int $size) : self;
}

