<?php

namespace Akademiano\Config;

use Akademiano\Config\Permanent\PermanentConfig;
use Akademiano\Config\Permanent\PermanentFabric;
use Akademiano\Config\Permanent\PermanentStorageInterface;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Object\Collection;
use Akademiano\Utils\Parts\DIContainerTrait;
use Pimple\Container;

class Config implements \ArrayAccess, \IteratorAggregate, DIContainerIncludeInterface
{
    use DIContainerTrait;

    const DYN_CONF = "__dynamic__";

    protected $configRaw;

    protected $childConfig = [];

    protected $environment;

    /** @var PermanentFabric */
    private $permanentFabric;

    public function __construct(array $config = [], Container $diContainer = null)
    {
        $this->set($config);
        if (null !== $diContainer) {
            $this->setDiContainer($diContainer);
        }
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->getDiContainer()["environment"];
    }

    /**
     * @return PermanentFabric
     */
    public function getPermanentFabric(): PermanentFabric
    {
        if (null === $this->permanentFabric) {
            $this->permanentFabric = new PermanentFabric();
        }
        return $this->permanentFabric;
    }

    public function set($data, array $path = null)
    {
        $this->childConfig = [];

        if (is_null($path)) {
            $this->configRaw = (array)$data;
            return;
        }
        $this->configRaw = ArrayTools::set($this->configRaw, $path, $data);
    }

    public function unset(array $path)
    {
        throw new \LogicException('Not Implemented');
        $this->childConfig = [];
        $this->configRaw = ArrayTools::set($this->configRaw, $path, null);
    }

    /**
     * @param array|Config $data
     * @return $this
     */
    public function joinLeft($data)
    {
        if ($data instanceof Config) {
            $data = $data->toArray();
        } else {
            $data = (array)$data;
        }
        $this->configRaw = ArrayTools::mergeRecursive($data, $this->configRaw);
        $this->childConfig = [];
        return $this;
    }

    public function joinRight($data)
    {
        if ($data instanceof Config) {
            $data = $data->toArray();
        } else {
            $data = (array)$data;
        }
        $this->configRaw = ArrayTools::mergeRecursive($this->configRaw, $data);
        $this->childConfig = [];
        return $this;
    }

    /**
     * @param array|string $path
     * @param null $default
     * @return Config|mixed|null
     */
    public function get($path = null, $default = null)
    {
        if (is_null($path)) {
            return $this;
        }
        $pathKey = implode('|', (array)$path);
        if (!isset($this->childConfig[$pathKey])) {
            if (!ArrayTools::issetByPath($this->configRaw, $path)) {
                return is_array($default) && !is_callable($default) ? new Config($default, $this->getDiContainer()) : $default;
            }
            $needConfig = ArrayTools::get($this->configRaw, $path, $default);
            if (is_array($needConfig)) {
                $firstElement = reset($needConfig);
                if (key($needConfig) === self::DYN_CONF && is_callable($firstElement)) {
                    $needConfig = call_user_func($firstElement, $this->getDiContainer());
                }
            }
            if (is_array($needConfig) && !is_callable($needConfig)) {
                $this->childConfig[$pathKey] = new Config($needConfig, $this->getDiContainer());
            } else {
                $this->childConfig[$pathKey] = $needConfig;
            }
        }
        return $this->childConfig[$pathKey];
    }

    public function getOrCall($path, callable $callback, array $arguments = null)
    {
        $data = $this->get($path);
        return $data ?? (empty($arguments) ? call_user_func($callback) : call_user_func_array($callback, $arguments));
    }

    public function getAndCall($path, $callback, array $arguments = null)
    {
        if (ArrayTools::issetByPath($this->configRaw, $path)) {
            $value = $this->get($path);
            if (null !== $arguments) {
                array_unshift($arguments, $value);
            } else {
                $arguments = [$value];
            }
            return call_user_func_array($callback, $arguments);
        }
        throw new \Exception(sprintf(
            'In config not found path \'%s\'',
            is_scalar($path) ? $path : json_encode($path, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)
        ));
    }

    /**
     * @param $path
     * @return Config|mixed
     * @throws \Exception
     */
    public function getOrThrow($path)
    {
        $data = $this->get($path);
        if (null === $data) {
            throw new \Exception(sprintf(
                'In config not found path \'%s\'',
                is_scalar($path) ? $path : json_encode($path, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)
            ));
        }
        return $data;
    }

    public function getOneIs(array $paths, $default = null)
    {
        foreach ($paths as $path) {
            $data = $this->get($path);
            if ($data) {
                return $data;
            }
        }
        return $default;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->configRaw[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset)) ? $this->get($offset) : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->configRaw[$offset] = $value;
        if (isset($this->childConfig[$offset])) {
            unset($this->childConfig[$offset]);
        }

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (isset($this->configRaw[$offset])) {
            unset($this->configRaw[$offset]);
        }
        if (isset($this->childConfig[$offset])) {
            unset($this->childConfig[$offset]);
        }
    }

    public function toArray()
    {
        return (array)$this->configRaw;
    }

    public function toCollection()
    {
        return new Collection($this->configRaw);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    public function toPermanent(?PermanentStorageInterface $storage, ?array $prefix): PermanentConfig
    {
        return $this->getPermanentFabric()->build($this, $storage, $prefix);
    }
}
