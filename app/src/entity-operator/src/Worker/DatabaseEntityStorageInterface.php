<?php


namespace Akademiano\EntityOperator\Worker;


interface DatabaseEntityStorageInterface extends EntityWorkerInterface, KeeperInterface, FinderInterface, GetterInterface, LoaderInterface, ReserveInterface, GenerateIdWorkerInterface
{

}
