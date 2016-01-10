<?php

namespace DeltaDb\Parts;

use DeltaDb\Adapter\AdapterInterface;

interface DbaIncludeInterface
{

    /**
     * @param \DeltaDb\Adapter\AdapterInterface $dao
     */
    public function setDba(AdapterInterface $dao);

    /**
     * @return \DeltaDb\Adapter\AdapterInterface
     */
    public function getDba();
}
