<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\EntityInterface;

class LoadCommand extends Command implements CommandInterface
{
    public function __construct(EntityInterface $entity, array $data)
    {
        parent::__construct(["entity" =>$entity, "data" => $data], null, self::COMMAND_LOAD);
    }

    public function getClass()
    {
        if (null === $this->class) {
            $entity = $this->getParams("entity");
            $this->class = (null !== $entity && is_object($entity)) ? get_class($entity) : "DeltaPhp\Operator\\Entity\\Entity";
        }
        return $this->class;
    }
}
