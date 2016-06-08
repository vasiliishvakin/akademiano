<?php


namespace DeltaPhp\Operator\Entity;


class TextEntity extends NamedEntity implements TextEntityInterface
{

    protected $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}
