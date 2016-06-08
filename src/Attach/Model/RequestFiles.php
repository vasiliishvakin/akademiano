<?php


namespace Attach\Model;

use DeltaUtils\Object\Collection;

class RequestFiles
{
    /** @var  Collection|File[] */
    protected $deleted;
    /** @var  Collection|File[] */
    protected $updated;
    /** @var  Collection|File[] */
    protected $uploaded;

    /**
     * @return File[]|Collection
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param File[]|Collection $deleted
     */
    public function setDeleted(Collection $deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return File[]|Collection
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param File[]|Collection $updated
     */
    public function setUpdated(Collection $updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return File[]|Collection
     */
    public function getUploaded()
    {
        return $this->uploaded;
    }

    /**
     * @param File[]|Collection $uploaded
     */
    public function setUploaded(Collection $uploaded)
    {
        $this->uploaded = $uploaded;
    }
}
