<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace News\Model;


use Attach\Model\FileManager;
use DeltaDb\AbstractEntity;
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








} 