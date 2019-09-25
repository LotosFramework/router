<?php

namespace Lotos\Router;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

interface ErrorPagesInterface
{
    public function notFound(RequestInterface $request) : ResponseInterface;
    public function notAllowed(RequestInterface $request) : ResponseInterface;
}
