<?php


namespace Attach\Model\Parts;


use Attach\Model\File;
use Attach\Model\Worker\FileWorkerTrait;
use DeltaUtils\Object\Collection;

trait AttachImagesTrait
{
    use FileWorkerTrait;
    /** @var  File[]|Collection */
    protected $images;

    /**
     * @return \Attach\Model\File[]|Collection
     */
    public function getImages()
    {
        return $this->images;
    }
}