<?php


namespace Akademiano\Attach\Model;


use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\Entity\NamedEntity;
use HttpWarp\File\Parts\FileProperties;
use HttpWarp\File\UploadFile;
use DeltaUtils\FileSystem;

class FileEntity extends NamedEntity implements EntityInterface
{
    use FileProperties;

    protected $type;
    protected $subType;
    protected $path;
    protected $fieldId;
    protected $size;
    protected $mimeType;
    /**
     * @var UploadFile
     */
    protected $uploadedFile;

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getName()
    {
        return $this->getTitle();
    }

    /**
     * @param mixed $subType
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;
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

    public function getRootDir()
    {
        return ROOT_DIR;
    }

    public function getFullPath()
    {
        return $this->getRootDir() . DIRECTORY_SEPARATOR . $this->getPath();
    }

    public function getFileName()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    public function getFileDirectory()
    {
        return pathinfo($this->getPath(), PATHINFO_DIRNAME);
    }

    /**
     * @return mixed
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * @param mixed $fieldId
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;
    }

    public function setUploadFile(UploadFile $file)
    {
       $this->uploadedFile = $file;
    }

    public function getUploadFile()
    {
        return $this->uploadedFile;
    }

    public function isUploaded()
    {
        return null === $this->uploadedFile;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        if (is_null($this->size)) {
            $this->size = filesize($this->getFullPath());
        }
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getMimeType()
    {
        if (is_null($this->mimeType)) {
            $this->mimeType = FileSystem::getFileType($this->getFullPath());
        }
        return $this->mimeType;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        if (null === $this->type) {
            $mime = $this->getMimeType();
            $types = explode("/", $mime);
            $this->type = $types[0];
            $this->subType = $types[1];
        }
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getSubType()
    {
        if (null === $this->subType) {
            $mime = $this->getMimeType();
            $types = explode("/", $mime);
            $this->type = $types[0];
            $this->subType = $types[1];
        }
        return $this->subType;
    }

    public function mv($path)
    {
        if (!$this->isMoved()) {
            $file = $this->getFullPath();
            $result = move_uploaded_file($file, $path);
            $this->isMoved = $result;
        } else {
            $result = rename($this->getPath(), $path);
        }
        if (!$result) {
            return false;
        }
        $this->path = $path;
        return $this->getPath();
    }

    /**
     * @param null $template
     * @return string
     * @deprecated 
     */
    public function getUri($template = null)
    {
        return $this->getUrl($template);
    }

    public function getUrl($template = null)
    {
        $fileDir = $this->getFileDirectory();
        if (strpos($fileDir, "public/") === 0) {
            $fileDir = substr($fileDir, 7);
        }
        if (null !== $template) {
            $dirs = explode("/", $fileDir);

            if (count($dirs) === 2) {
                $fileDir = $template . "/" . $fileDir;
            } else {
                array_splice($dirs, -2, 0, $template);
                $fileDir = implode("/", $dirs);
            }
        }

        return "/" . $fileDir . "/" . $this->getFileName();
    }
}
