<?php


namespace Akademiano\Db\Adapter\D2QL;

use Akademiano\Db\Adapter\PgsqlAdapter;


abstract class Query extends Element implements QueryInterface
{
    /**
     * Query constructor.
     * @param PgsqlAdapter $adapter
     */
    public function __construct(PgsqlAdapter $adapter = null)
    {
        if ($adapter) {
            $this->setAdapter($adapter);
        }
    }
}
