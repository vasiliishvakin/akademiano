<?php


namespace Akademiano\Entity;


class ContentEntity extends NamedEntity implements ContentEntityInterface
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

    public function toArray():array
    {
        $data = parent::toArray();
        $data['content'] = $this->getContent();
        return $data;
    }


}
