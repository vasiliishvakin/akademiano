<?php


namespace Akademiano\Content\Files\Images\Model;


use Akademiano\Attach\Model\LinkedFile;

class LinkedImage extends LinkedFile
{
    /** @var bool */
    protected $isMain = false;

    protected $order = 0;

    protected $imagesize;

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
            $isMain = (bool)$isMain;
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

    public function getImagesize(): ?array
    {
        if (null === $this->imagesize) {
            $filepath = $this->getFullPath();
            $this->imagesize = getimagesize($filepath);
            if (!$this->imagesize) {
                throw new \Exception(sprintf("Error in get imagesize in file %s", $filepath));
            }
        }
        return $this->imagesize;
    }

    public function getWidth(): int
    {
        return $this->getImagesize()[0];
    }

    public function getHeight(): int
    {
        return $this->getImagesize()[1];
    }

    public function getRatio(): float
    {
        return $this->getWidth() / $this->getHeight();
    }

    public function isRatioLike(float $needRatio, float $percent = 10): bool
    {
        $valPart = ($needRatio / 100) * $percent;
        $min = $needRatio - $valPart;
        $max = $needRatio + $valPart;
        return  $this->getRatio() > $min && $this->getRatio() < $max;
    }
}
