<?php


namespace Akademiano\Db\Adapter\D2QL;

use Akademiano\Db\Adapter\PgsqlAdapter;

interface ElementInterface
{
    public function getAdapter();

    public function setAdapter(PgsqlAdapter $adapter);

    public function escapeIdentifier($string);

    public function escape($value);
}
