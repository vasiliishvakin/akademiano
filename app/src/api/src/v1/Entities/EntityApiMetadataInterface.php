<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\ApiInterface;

interface EntityApiMetadataInterface extends ApiInterface
{
    public function getFields(): array;

    public function getFormFields(): array;
}
