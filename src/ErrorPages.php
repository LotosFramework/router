<?php

namespace Lotos\Router;

use Psr\Http\Message\{RequestInterface, ResponseFactoryInterface, ResponseInterface};
use Fig\Http\Message\StatusCodeInterface;

class ErrorPages implements ErrorPagesInterface, StatusCodeInterface
{
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function notFound(RequestInterface $request) : ResponseInterface
    {
        return $this->responseFactory->createResponse(self::STATUS_NOT_FOUND, 'Not found');
    }

    public function notAllowed(RequestInterface $request) : ResponseInterface
    {
        return $this->responseFactory->createResponse(self::STATUS_METHOD_NOT_ALLOWED, 'Method not allowed');
    }

    public function invalidOrigin(RequestInterface $request) : ResponseInterface
    {
        return $this->responseFactory->createResponse(self::STATUS_LOCKED, 'Locked');
    }
}
