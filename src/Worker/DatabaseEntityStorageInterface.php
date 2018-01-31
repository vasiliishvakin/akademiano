<?php


namespace Akademiano\EntityOperator\Worker;


interface DatabaseEntityStorageInterface extends EntityWorkerInterface, KeeperInterface, FinderInterface, LoaderInterface, ReserveInterface, GenerateIdWorkerInterface
{

}
