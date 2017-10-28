<?php


namespace Akademiano\Delegating\Command;


use Akademiano\Utils\ArrayTools;

class Command implements CommandInterface
{
    const COMMAND_NAME = CommandInterface::COMMAND_UNDEFINED;

    protected $name;
    protected $class;
    /** @var  array|null */
    protected $params;

    /**
     * Command constructor.
     * @param $name
     * @param $class
     * @param array $params
     */
    public function __construct(array $params = null, $class = null, $name = null)
    {
        $this->name = $name ? $name : static::COMMAND_NAME;
        if (null !== $class) $this->class = $class;
        if (null !== $params) $this->setParams($params);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    public function hasParam($path)
    {
        if (null === $this->params) {
            return false;
        }
        return ArrayTools::issetByPath($this->params, $path);
    }

    /**
     * @return array
     */
    public function getParams($path = null, $default = null)
    {
        if (null === $this->params) {
            return $default;
        }
        if (null !== $path) {
            return ArrayTools::get($this->params, $path, $default);
        }
        return $this->params;
    }

    /**
     * @param array $params
     * @param array|null $path
     */
    public function setParams(array $params, $path = null)
    {
        if (null !== $path) {
            if (null === $this->params) {
                $this->params = [];
            }
            $this->params = ArrayTools::set($this->params, $path, $params);
        } else {
            $this->params = $params;
        }
    }

    public function addParams($params, $path = null)
    {
        $this->params = ArrayTools::add($this->params, $path, $params);
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isEmptyClass()
    {
        return !$this->hasClass();
    }

    public function hasClass()
    {
        return !empty($this->getClass());
    }
}
