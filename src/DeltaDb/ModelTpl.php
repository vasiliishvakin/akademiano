<?php

class ModelTpl
{
    protected $class;

    protected $model;

    protected $request;

    function __construct($class)
    {
        $this->setClass($class);
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return ModelDbExternal
     */
    public function getModel()
    {
        return call_user_func($this->getClass() . '::model');
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            throw new LogicException('not implemented');
        }
        return $this->request;
    }

    public function getList($activeId = null)
    {
        $itemsObj = $this->getModel()->getAll();
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

    public function tplList(ViewBlitz $template, $activeId = null)
    {
        $activeId = $activeId ?: $this->getId();
        return $template->assign($this->getPrefix() . 'List', $this->getList($activeId));
    }

    public function getItem($id = null)
    {
        $id = $id ?: $this->getId();
        $item = $this->getModel()->getById($id);
        $item = empty($item) ? null : $item->toArray();
        return $item;
    }

    public function tplItem(ViewBlitz $template, $id = null)
    {
        return $template->assign($this->getPrefix(), $this->getItem($id));
    }

    public function getPrefix()
    {
        return lcfirst($this->getClass());
    }

    public function tplItemParams(ViewBlitz $template, $id = null, $prefix = null)
    {
        $id = $id ?: $this->getId();
        $item = $this->getItem($id);
        $prefix = $prefix ?: $this->getPrefix();
        $params = [];
        foreach ($item as $name=>$value) {
            $name = $prefix . ucfirst($name);
            $params[$name] = $value;
        }
        return $template->assignArray($params);
    }

    public function reqGetFields()
    {
        $request = $this->getRequest();
        $dbFields = $this->getModel()->getDbFields();
        $dbFields[] = 'id';
        $newFields = $request->getParams($dbFields);
        $newFields = array_filter($newFields, function ($var) {return !is_null($var);});
        return $newFields;
    }

    public function reqDelete($id = null)
    {
        $id = $id ?: $this->getId();
        return $this->getModel()->deleteById($id);
    }


}