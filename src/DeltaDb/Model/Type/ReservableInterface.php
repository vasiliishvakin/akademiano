<?php


namespace DeltaDb\Model\Type;


interface ReservableInterface
{
    /**
     * @param null|string|\DeltaDb\Adapter\AdapterInterface $adapter
     * @return string|integer|null
     * @throws \LogicException
     */
    public function toReserve( $adapter = null);

}
