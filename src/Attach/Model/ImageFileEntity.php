<?php

namespace Akademiano\Attach\Model;


use DeltaPhp\Operator\Entity\NamedEntityInterface;

class ImageFileEntity extends FileEntity implements NamedEntityInterface
{
    /** @var  bool */
    protected $main;
    /** @var  integer */
    protected $order = 0;

    /**
     * @return boolean
     */
    public function isMain()
    {
        return $this->main;
    }

    /**
     * @param boolean $main
     */
    public function setMain($main)
    {
        if (is_string($main)) {
            switch ($main) {
                case "f":
                case "false":
                    $main = false;
                    break;
                case "t":
                case "true":
                    $main = true;
                    break;
                default:
                    throw  new \RuntimeException("bool string may be only f(false) or t(true)");
            }
        }
        $this->main = $main;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = (integer)$order;
    }
}
