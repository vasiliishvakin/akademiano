<?php


namespace Akademiano\Content\Files\Images\Model;


use Akademiano\Attach\Model\LinkedFile;

class LinkedImage extends LinkedFile
{
    /** @var bool  */
    protected $isMain = false;

    protected $order = 0;

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->isMain;
    }

    /**
     * @param bool $isMain
     */
    public function setMain($isMain): void
    {
        if ($isMain === "t" || $isMain === "true") {
            $isMain = true;
        } elseif ($isMain === "f" || $isMain === "false") {
            $isMain = false;
        } else {
            $isMain =(bool) $isMain;
        }
        $this->isMain = $isMain;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
    }
}
