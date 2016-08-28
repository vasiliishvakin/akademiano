<?php


namespace DeltaDb\D2QL;

use DeltaDb\Adapter\PgsqlAdapter;

interface ElementInterface
{
    public function getAdapter();

    public function setAdapter(PgsqlAdapter $adapter);

    public function escapeIdentifier($string);

    public function escape($value);
}
