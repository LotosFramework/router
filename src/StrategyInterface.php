<?php

namespace Lotos\Router;

use Psr\Http\Message\ResponseInterface;

interface StrategyInterface
{
    public function process(ResponseInterface $response);
}
