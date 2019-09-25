<?php

namespace Lotos\Router;

use Psr\Http\Message\{RequestInterface, ResponseFactoryInterface, ResponseInterface};

class ErrorPages implements ErrorPagesInterface
{
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function notFound(RequestInterface $request) : ResponseInterface
    {
        return $this->responseFactory->createResponse(404, 'Not found');
    }

    public function notAllowed(RequestInterface $request) : ResponseInterface
    {
        return $this->responseFactory->createResponse(405, 'Method not allowed');
    }

    public function invalidOrigin(RequestInterface $request) : ResponseInterface
    {
        return $this->responseFactory->createResponse(423, 'Locked');
    }
}
