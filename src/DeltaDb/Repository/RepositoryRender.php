<?php

namespace DeltaDb\Repository;

use OrbisTools\Parts\GetRequest;
use DeltaBind\Render\DataAssign as Render;

/**
 * Class RenderData
 * @package DeltaDb\Repository
 * @method  __construct(array $params) Params: ['class', 'table' = null, 'dbaName' => null]
 */
class RepositoryRender extends AbstractRepositoryAdditional
{
    use GetRequest;

    public function getList($activeId = null)
    {
        $itemsObj = $this->getRepository()->getAll();
        $items = [];
        foreach ($itemsObj as $obj) {
            $item = $obj->toArray();
            if ($activeId && $item['id'] == $activeId) {
                $item['_active'] = true;
            }
            $items[] = $item;
        }
        return $items;
    }

    public function getId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function setList(Render $render, $activeId = null)
    {
        $activeId = $activeId ? : $this->getId();
        return $render->assignVar($this->getPrefix() . 'List', $this->getList($activeId));
    }

    public function getItem($id = null)
    {
        $id = $id ? : $this->getId();
        $item = $this->getRepository()->getById($id);
        $item = empty($item) ? null : $item->toArray();
        return $item;
    }

    public function setItem(Render $render, $id = null)
    {
        return $render->assignVar($this->getPrefix(), $this->getItem($id));
    }

    public function getPrefix()
    {
        return lcfirst($this->getClass());
    }

    public function setItemValues(Render $render, $id = null, $prefix = null)
    {
        $id = $id ? : $this->getId();
        $item = $this->getItem($id);
        $prefix = $prefix ? : $this->getPrefix();
        $params = [];
        foreach ($item as $name => $value) {
            $name = $prefix . 'Item' . ucfirst($name);
            $params[$name] = $value;
        }
        return $render->assignArray($params);
    }


}