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
        return isset($this->items[$id]);
    }


    public function get($id)
    {
        if (!isset($this->items[$id])) {
            return null;
        }
        return $this->items[$id];
    }

    public function set($id, $item)
    {
        $this->items[$id] = $item;
    }

    public function rm($id)
    {
        unset($this->items[$id]);
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
        $currIds = array_keys($this->items);
        if (empty($currIds)) {
            return $ids;
        }
        return array_diff($ids, array_intersect($currIds, $ids));
    }

    public function getIds(array $ids)
    {
        $items = [];
        foreach($ids as $id) {
            $items[] = $this->get($id);
        }
        return $items;
    }

} 