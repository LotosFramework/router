<?php

namespace Lotos\Router;

use Lotos\Collection\Collection;

class RouteCollection extends Collection implements RouteCollectionInterface
{

    public function __construct($data = null)
    {
        parent::__construct($data);
    }

    public function addRoute(Route $route) : void
    {
        $this->push($route);
    }

    public function getRouteByPath(string $method, string $path) : ?Route
    {
        $routes = parent::where('path', $path);
        if($routes->count() == 1) {
            return $routes->first();
        } elseif($routes->count() == 0) {
            return null;
        } elseif($routes->count() > 1) {
            $routes = new self($routes->toArray());
            return $routes->where('method', $method)->first();
        }
    }

    public function getRouteByUri(string $method, string $uri) : ?Route
    {
        $routes = $this->where('uri', $uri);
        if($routes->count() == 1) {
            return $routes->first();
        } elseif($routes->count() == 0) {
            $pathArr = explode('/', $uri);
            $routes = $this->getRoutesBySize(count($pathArr));
            $routes = $routes->filter(function($route) use ($pathArr) {
                $routeArr = explode('/', $route->getUri());
                $diff1 = array_diff_assoc($routeArr, $pathArr);
                $diff2 = array_diff_assoc($pathArr, $routeArr);
                if(count($diff1) == count($diff2)) {
                    $results = [];
                    foreach($diff1 as $k => $v) {
                        if($this->isVar($v)) {
                            array_push($results, $this->isValidType($v, $pathArr[$k]));
                        } else {
                            array_push($results, $v == $pathArr[$k]);
                        }
                    }
                    if((!in_array(false, $results)) && (in_array(true, $results))) {
                        return true;
                    }
                    return false;
                }
            });
            if($routes->count() == 1) {
                return $routes->first();
            } elseif($routes->count() > 1) {
                $routes = new self($routes->toArray());
                return ($routes->where('method', $method)->count() > 0)
                    ? $routes->where('method', $method)->first()
                    : $routes->first();
            } else {
                return null;
            }
        } elseif($routes->count() > 1) {
            $routes = new self($routes->toArray());
                return ($routes->where('method', $method)->count() > 0)
                    ? $routes->where('method', $method)->first()
                    : $routes->first();
        }
    }

    public function getRoutesBySize(int $size) : RouteCollectionInterface
    {
        return new self($this->filter(function($route) use ($size) {
            return ($route->getSize() === $size);
        })->toArray());

    }

    private function isVar($var) : bool {
        if((substr($var, 0, 1) == '{') && ((substr($var, -1) == '}') || (substr($var, -2) == '}?'))) {
            return true;
        }
        return false;
    }

    private function isValidType($var, $path) : bool {
        if(($this->getVarType($var) == 'd') &&
            (is_numeric($path))) {
            return true;
        }
        if(($this->getVarType($var) == 'a') &&
           (ctype_alpha($path))) {
            return true;
        }
        if($this->getVarType($var) == 's') {
            return true;
        }
        return false;
    }

    private function getVarType($var) {
        return substr($var, -2, 1);
    }
}
