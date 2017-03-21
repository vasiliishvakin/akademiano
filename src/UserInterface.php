<?php


namespace Akademiano\Entity;



interface UserInterface extends NamedEntityInterface
{
    /**
     * @return GroupInterface
     */
    public function getGroup();
}
