<?php


namespace Akademiano\EntityOperator\Command;


class DeleteCommand extends EntityDataObjectCommand
{
    public function getEntityId()
    {
        if (!empty($this->data)) {
            if (isset($this->data['id'])) {
                return $this->data['id'];
            }
        }
        return $this->getEntity()->getId();
    }
}
