<?php


namespace Akademiano\Attach\Model;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;

class RequestFileInfo implements ArrayableInterface
{
    /** @var int */
    protected $id;
    /** @var string */
    protected $title;
    /** @var string */
    protected $description;
    /** @var int */
    protected $order;
    /** @var bool */
    protected $isMain = false;

    public function __construct(array $data = null)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }


    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getOrder(): ?int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->isMain;
    }

    public function setMain(bool $isMain): void
    {
        $this->isMain = $isMain;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'order' => $this->getOrder(),
            'main' => $this->isMain(),
        ];
    }
}
