<?php


namespace Akademiano\Db\Adapter\Type;


interface ReservableInterface
{
    /**
     * @param null|string|\DeltaDb\Adapter\AdapterInterface $adapter
     * @return string|integer|null
     * @throws \LogicException
     */
    public function toReserve( $adapter = null);

}
