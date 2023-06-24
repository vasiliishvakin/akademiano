<?php

namespace Akademiano\Utils\Parts;


trait InnerCache
{
    protected $innerCache = [];

    public function setInnerCache($id, $data)
    {
        $this->innerCache[$id] = $data;
    }

    public function getInnerCache($id)
    {
        return (!isset($this->innerCache[$id])) ? null : $this->innerCache[$id];
    }

    public function hasInnerCache($id)
    {
        return array_key_exists($id, $this->innerCache);
    }

}
