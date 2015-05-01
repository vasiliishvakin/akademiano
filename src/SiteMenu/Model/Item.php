<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace SiteMenu\Model;
use DeltaCore\Parts\MagicSetGetManagers;
use DeltaCore\Prototype\MagicMethodInterface;

/**
 * Class Item
 * @package SiteMenu\Model
 * @method setAclManager(\Acl\Model\AclManager $manager)
 * @method \Acl\Model\AclManager getAclManager()
 */
class Item implements MagicMethodInterface
{
    use MagicSetGetManagers;

    protected $id;
    protected $text;
    protected $title;
    protected $link;
    protected $order = 0;
    protected $active = false;

    function __construct($data = null)
    {
        if ($data) {
            foreach($data as $name=>$value) {
                $method = "set" . ucfirst($name);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                }
            }
        }
    }


    public function getId()
    {
        if (is_null($this->id)) {
            $this->id = hash("md5", $this->getLink());
        }
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->id = null;
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
        $this->order = (integer) $order;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    public function isAllow($user = null)
    {
        if ($aclManager = $this->getAclManager()) {
            return $aclManager->isAllow($this->getLink(), $user);
        }
        return true;
    }

}