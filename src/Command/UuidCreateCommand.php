<?php


namespace Akademiano\UUID\Command;


use Akademiano\Delegating\Command\Command;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\Entity;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\UUID\UuidComplexShortTables;

class UuidCreateCommand extends CreateCommand  implements CommandInterface
{

    protected $value;

    protected $shard;

    protected $table;

    protected $epoch;

    public function __construct(string $class = UuidComplexShortTables::class)
    {
        parent::__construct($class);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): UuidCreateCommand
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShard()
    {
        return $this->shard;
    }

    public function setShard($shard): UuidCreateCommand
    {
        $this->shard = $shard;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    public function setTable($table): UuidCreateCommand
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEpoch()
    {
        return $this->epoch;
    }

    /**
     * @param mixed $epoch
     */
    public function setEpoch($epoch): void
    {
        $this->epoch = $epoch;
    }

}
