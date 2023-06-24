<?php

namespace Akademiano\Content\Files\Model;

use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Entity\NamedEntity;
use Akademiano\HttpWarp\File\Parts\FileProperties;
use Akademiano\UserEO\Model\Utils\OwneredTrait;
use Akademiano\Utils\FileSystem;
use Akademiano\HttpWarp\File\UploadFile;

class File extends NamedEntity implements DelegatingInterface
{
    use FileProperties;

    protected $rootDir;
    protected $dataDir;

    protected $type;
    protected $subType;
    protected $path;
    protected $position;
    protected $size;
    protected $mimeType;
    /** @var  bool */
    protected $moved = false;
    protected $extension;

    /**
     * @var UploadFile
     */
    protected $uploadedFile;

    use DelegatingTrait;
    use OwneredTrait;

    /**
     * @return bool
     */
    protected function isMoved()
    {
        return $this->moved;
    }

    /**
     * @param bool $moved
     */
    protected function setMoved($moved)
    {
        $this->moved = $moved;
    }

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

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getRootDir():string
    {
        if (null === $this->rootDir) {
            if (defined("ROOT_DIR")) {
                $this->rootDir = ROOT_DIR;
            } else {
                $rootDir = realpath(__DIR__ . '/../../../../');
                if ($rootDir && is_dir($rootDir . DIRECTORY_SEPARATOR . 'vendor')) {
                    $this->rootDir = $rootDir;
                } else {
                    throw new \RuntimeException("Root dir not defined and not found");
                }
            }
        }
        return $this->rootDir;
    }

    public function getDataDir(): string
    {
        if (null === $this->dataDir) {
            if (defined("DATA_DIR")) {
                $this->dataDir = DATA_DIR;
            } else {
                $rootDir = $this->getRootDir();
                if ($rootDir && is_dir($rootDir . DIRECTORY_SEPARATOR . 'data')) {
                    $this->dataDir = $rootDir . DIRECTORY_SEPARATOR . 'data';
                } else {
                    throw new \RuntimeException("Data dir not defined and not found");
                }
            }
        }
        return $this->dataDir;
    }


    public function getFullPath()
    {
        return $this->getDataDir() . DIRECTORY_SEPARATOR . $this->getPath();
    }

    public function getFileName()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    public function getFileDirectory()
    {
        return pathinfo($this->getPath(), PATHINFO_DIRNAME);
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
            $this->setMoved($result);
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

    /** @deprecated  */
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

    public function getExtension()
    {
        if (null === $this->extension) {
            $command = new MimeyExtensionCommand($this);
            $this->extension = $this->delegate($command);
        }
        return $this->extension;
    }

    public function toArray():array
    {
        $data = parent::toArray();
        $data['extension'] = $this->getExtension();
        $data['position'] = $this->getPosition();
        return $data;
    }
}
