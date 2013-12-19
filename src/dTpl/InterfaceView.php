<?php

namespace dTpl;

interface InterfaceView
{
    const DEFAULT_TEMPLATE = 'default';

    public function setTemplate($name);

    public function setArrayTemplates($templates);

    public function addArrayTemplate($templateString, $name = self::DEFAULT_TEMPLATE);

    public function assign($name, $value);

    public function assignArray(array $array);

    public function getAssignedVars();

    public function addGlobalVar($name, $value);

    public function getGlobalVars();

    public function render($params = [], $templateName = null);

}