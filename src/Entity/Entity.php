<?php


namespace EntityOperator\Entity;


class Entity implements EntityInterface
{
    protected $id;
    /** @var  \DateTime */
    protected $created;
    /** @var  \DateTime */
    protected $changed;
    /** @var  boolean */
    protected $active;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param \DateTime $changed
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        if ($active === "t" || $active === "true") {
            $active = true;
        } elseif ($active === "f" || $active === "false") {
            $active = false;
        }
        $this->active = (boolean) $active;
    }

    

}