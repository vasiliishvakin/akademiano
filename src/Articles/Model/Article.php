<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Model;

use Attach\Model\FileManager;
use DeltaCore\Prototype\MiddleObject;
use DeltaDb\EntityInterface;
use DeltaUtils\StringUtils;
use DictDir\Model\ComboDirectoryManager;
use DictDir\Model\UniDirectoryManager;

class Article extends MiddleObject implements EntityInterface
{
    protected $categories = [];
    protected $title;
    protected $text;
    protected $images;

    /** @var  UniDirectoryManager|ComboDirectoryManager */
    protected $categoryManager;

    /** @var  FileManager */
    protected $fileManager;


    /**
     * @param mixed $categoryManager
     */
    public function setCategoryManager($categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @return mixed
     */
    public function getCategoryManager()
    {
        return $this->categoryManager;
    }

    /**
     * @param \Attach\Model\FileManager $fileManager
     */
    public function setFileManager($fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @return \Attach\Model\FileManager
     */
    public function getFileManager()
    {
        return $this->fileManager;
    }

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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = (integer) $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
        $this->categories = (array) $categories;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        if (empty($this->categories)) {
            return [];
        }
        if (!is_object($this->categories[0])) {
            $refMan = $this->getCategoryManager();
            $categories = $refMan->findByIds($this->categories);
            $this->categories = (array) $categories;
        }
        return $this->categories;
    }

    public function getCategoriesIds()
    {
        $categories = $this->getCategories();
        $ids = [];
        foreach($categories as $category) {
            $ids[] = $category->getId();
        }
        return $ids;
    }

    public function getImages()
    {
        if (is_null($this->images)) {
            $fm = $this->getFileManager();
            $this->images = $fm->getFilesForObject($this);
        }
        return $this->images;
    }

    public function getTitleImage()
    {
        $images = $this->getImages();
        if (empty($images)) {
            return null;
        }
        return reset($images);
    }








} 