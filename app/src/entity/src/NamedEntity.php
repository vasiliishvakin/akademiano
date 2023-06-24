<?php

namespace Akademiano\Entity;


class NamedEntity extends Entity implements NamedEntityInterface
{
    protected $title;
    protected $description;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function toArray():array
    {
        $data = parent::toArray();
        $data['title'] = $this->getTitle();
        $data['description'] = $this->getDescription();
        return $data;
    }


}
