<?php

namespace Akademiano\HttpWarp\Request;

use Akademiano\Utils\ArrayTools;

class ParamsStructure
{

    /** @var array|StructureItem[] */
    protected array $params = [];

    /**
     * ParamsStructure constructor.
     * @param StructureItem[]|array $params
     */
    public function __construct(array $paramsData)
    {
        $this->load($paramsData);
    }

    public function addParamData(string $name, ?callable $filter = null, $default = null, int $flags = 0, ?string $alias = null): ParamsStructure
    {
        return $this->addParam(new StructureItem($name, $filter, $default, $flags, $alias));
    }

    public function addParam(StructureItem $item): ParamsStructure
    {
        $this->params[$item->getName()] = $item;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function load(array $data)
    {
        $defaultStructure = [
            'alias' => null,
            'filter' => null,
            'default' => null,
            'flags' => 0,
        ];
        if (ArrayTools::getArrayType($data) === ArrayTools::ARRAY_TYPE_NUM) {
            $data = array_fill_keys($data, null);
        }

        foreach ($data as $name => $value) {
            $value = array_merge($defaultStructure, array_intersect_key((array)$value, $defaultStructure));
            $this->addParamData($name, $value['filter'], $value['default'], $value['flags'], $value['alias']);
        }
    }
}
