<?php

namespace Lotos\Router;

use \Closure;
use Lotos\Http\StrategyInterface;

trait StrategyTrait
{

    public function setStrategy(StrategyInterface $strategy) : self
    {
        if($this instanceof Group) {
            $this->setGroupStrategy($this->routeCollection
                ->whereContain('prefix', $this->prefix)
                ->whereNull('strategy'),
                $strategy);
        } else {
            $this->route->setStrategy($strategy);
        }
        return $this;
    }

}
