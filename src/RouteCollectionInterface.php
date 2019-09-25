<?php

namespace Lotos\Router;

use Lotos\Collection\Collection;

interface RouteCollectionInterface extends Collection
{
    public function addRoute(Route $route) : void;
    public function getRouteByPath(string $method, string $path) : ?Route;
    public function getRouteByUri(string $method, string $uri) : ?Route;
    public function getRoutesBySize(int $size) : self;
}

