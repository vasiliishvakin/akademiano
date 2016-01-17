<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Model;

use Attach\Model\Parts\GetImagesTrait;
use DeltaCore\Parts\MagicSetGetManagers;
use DeltaCore\Prototype\MiddleObject;
use DeltaDb\EntityInterface;
use DeltaUtils\StringUtils;

/**
 * Class Article
 * @package Articles
 * @method  setCategoryManager(\DictDir\Model\UniDirectoryManager $categoryManager)
 * @method  \DictDir\Model\UniDirectoryManager getCategoryManager()
 * @method  setFileManager(\Attach\Model\FileManager $fileManager)
 * @method \Attach\Model\FileManager getFileManager()
 */

class Article extends MiddleObject implements EntityInterface
{
    use MagicSetGetManagers;
    use GetImagesTrait;

    protected $categories = [];
    protected $title;
    protected $text;

    /**
     * @return mixed
     */
    public function getDescription()
    {
        if (is_null($this->description)) {
            $text = $this->getText();
            $this->description = StringUtils::cutStr($text, 160);
        }
        return $this->description;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = (array)$categories;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        if (empty($this->categories)) {
            return [];
        }
        if (is_array($this->categories)) {
            $refMan = $this->getCategoryManager();
            $categories = $refMan->findByIds($this->categories);
            $this->categories = $categories;
        }
        return $this->categories;
    }

    public function getCategoriesIds()
    {
        $categories = $this->getCategories();
        $ids = [];
        foreach ($categories as $category) {
            $ids[] = $category->getId();
        }
        return $ids;
    }
}
