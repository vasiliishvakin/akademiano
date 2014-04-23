<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace News\Model;


use Attach\Model\FileManager;
use DeltaDb\AbstractEntity;
use DeltaUtils\StringUtils;
use DictDir\Model\ComboDirectoryManager;
use DictDir\Model\UniDirectoryManager;

class Article extends AbstractEntity
{
    protected $id;
    protected $categories = [];
    protected $title;
    protected $description;
    protected $text;
    protected $created;
    protected $changed;
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
     * @param mixed $created
     */
    public function setCreated($created)
    {
        if (!$created instanceof \DateTime) {
            $created = new \DateTime($created);
        }
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param mixed $changed
     */
    public function setChanged($changed)
    {
        if (!$changed instanceof \DateTime) {
            $changed = new \DateTime($changed);
        }
        $this->changed = $changed;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
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