<?php


namespace Akademiano\Acl\XAclConf;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;

class Item implements ArrayableInterface
{
    const ACCESS_ALLOW = "allow";
    const ACCESS_DENY = "deny";

    protected $path;
    protected $access = self::ACCESS_ALLOW;

    /**
     * Item constructor.
     * @param $path
     * @param string $access
     */
    public function __construct($path = null, $access = null)
    {
        if (null !== $path) {
            $this->setPath($path);
        }
        if (null !== $access) {
            $this->setAccess($access);
        }
    }


    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access = true)
    {
        if (null === $access) {
            $this->setAllow();
        }
        if (is_bool($access)) {
            if ($access) {
                $this->setAllow();
            } else {
                $this->setDeny();
            }
        } elseif (is_numeric($access)) {
            $access = (int)$access;
            if ($access > 0) {
                $this->setAllow();
            } else {
                $this->setDeny();
            }
        } elseif (is_string($access)) {
            $access = strtolower((trim($access)));
            switch ($access) {
                case self::ACCESS_ALLOW :
                    $this->setAllow();
                    break;
                case self::ACCESS_DENY:
                    $this->setDeny();
                    break;
                default:
                    throw new \InvalidArgumentException("Access value not recognize");
            }
        } else {
            throw new \InvalidArgumentException("Access value not recognize");
        }
    }

    public function setAllow()
    {
        $this->access = self::ACCESS_ALLOW;
    }

    public function setDeny()
    {
        $this->access = self::ACCESS_DENY;
    }

    public function isAllow()
    {
        return $this->access === self::ACCESS_ALLOW;
    }

    public function isDeny()
    {
        return $this->access === self::ACCESS_DENY;
    }

    public function toArray()
    {
        return [
            "path" => $this->getPath(),
            "access" => $this->getAccess(),
        ];
    }

    public function __toString()
    {
        return $this->getPath() . "=" . $this->getAccess();
    }
}
