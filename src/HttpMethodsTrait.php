<?php

namespace Lotos\Router;

trait HttpMethodsTrait
{

    abstract public function map(string $method, string $path, $handler);

    public function get(string $path, $handler) : self
    {
        $this->map(self::METHOD_GET, $path, $handler);
        return $this;
    }

    public function post(string $path, $handler) : self
    {
        $this->map(self::METHOD_POST, $path, $handler);
        return $this;
    }

    public function put(string $path, $handler) : self
    {
        $this->map(self::METHOD_PUT, $path, $handler);
        return $this;
    }

    public function patch(string $path, $handler) : self
    {
        $this->map(self::METHOD_PATCH, $path, $handler);
        return $this;
    }

    public function delete(string $path, $handler) : self
    {
        $this->map(self::METHOD_DELETE, $path, $handler);
        return $this;
    }

    public function options(string $path, $handler) : self
    {
        $this->map(self::METHOD_OPTIONS, $path, $handler);
        return $this;
    }

    public function head(string $path, $handler) : self
    {
        $this->map(self::METHOD_HEAD, $path, $handler);
        return $this;
    }

    public function any(string $path, $handler) : self
    {
        $this->map(self::METHOD_ANY, $path, $handler);
        return $this;
    }

}
