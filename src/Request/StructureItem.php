<?php


namespace Akademiano\HttpWarp\Request;


class StructureItem
{
    const PARAM_MANDATORY = 2;

    protected string $name;

    protected ?\Closure $filter;

    protected $default = null;

    protected int $flags = 0;

    protected ?string $alias;

    public function __construct(string $name, ?\Closure $filter = null, $default = null, int $flags = 0, ?string $alias = null)
    {
        $this->name = $name;
        $this->filter = $filter;
        $this->default = $default;
        $this->flags = $flags;
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \Closure
     */
    public function getFilter(): ?\Closure
    {
        return $this->filter;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @param string|null $alias
     */
    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * @param \Closure $filter
     */
    public function setFilter(\Closure $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default): void
    {
        $this->default = $default;
    }

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @param int $flags
     */
    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    public function isMandatory()
    {
        return ($this->getFlags() && self::PARAM_MANDATORY);
    }
}
