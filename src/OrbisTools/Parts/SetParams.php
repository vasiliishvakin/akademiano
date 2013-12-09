<?php

namespace OrbisTools\Parts;

trait SetParams
{
    public function setParams(array $params = [])
    {
        foreach ($params as $name=>$value) {
            $method = 'set' . ucfirst($name);
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }
}