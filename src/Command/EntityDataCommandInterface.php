<?php


namespace Akademiano\EntityOperator\Command;


interface EntityDataCommandInterface extends EntityObjectCommandInterface
{
    public function getData(): ?array;

    public function setData(array $data);
}
