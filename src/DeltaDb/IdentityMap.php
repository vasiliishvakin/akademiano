<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb;


class IdentityMap
{
    protected $items = [];

    public function has($id)
    {
        return isset($this->items[(string)$id]);
    }


    public function get($id)
    {
        $id = (string)$id;
        if (!isset($this->items[$id])) {
            return null;
        }
        return $this->items[$id];
    }

    public function set($id, $item)
    {
        $this->items[(string)$id] = $item;
    }

    public function rm($id)
    {
        unset($this->items[(string)$id]);
    }

    public function getAll()
    {
        return $this->items;
    }

    public function getCount()
    {
        return count($this->items);
    }

    public function getIntersect(array $ids)
    {
        $currIds = array_keys($this->items);
        if (empty($currIds)) {
            return [];
        }
        return array_intersect($currIds, $ids);
    }

    public function getDiff(array $ids)
    {
        $ids = array_map(function($value) {return (string) $value;}, $ids);
        $currIds = array_keys($this->items);
        if (empty($currIds)) {
            return $ids;
        }
        return array_diff($ids, array_intersect($currIds, $ids));
    }

    public function getIds(array $ids)
    {
        $ids = array_map(function($value) {return (string) $value;}, $ids);
        $items = [];
        foreach ($ids as $id) {
            $item = $this->get($id);
            if ($item) {
                $items[] = $item;
            }
        }
        return $items;
    }

} 
