<?php

namespace Akademiano\Core;


use Pimple\Container;

class DI extends Container
{
    public function lazyGet($id)
    {
        return function() use ($id) {
            return $this[$id];
        };
    }
}
