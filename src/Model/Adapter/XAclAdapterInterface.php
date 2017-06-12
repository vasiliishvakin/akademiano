<?php

namespace Akademiano\Acl\Model\Adapter;


use Akademiano\Acl\Model\XAclConf\File;
use Akademiano\Utils\ArrayTools;
use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

class XAclAdapterInterface implements AdapterInterface, FileBasedAdapterInterface
{
    const DATA_DIR_NAME = "acl";
    const DATA_ROOT_DIR_NAME = "data";

    const ROOT_RESOURCE_PATH = "__root__";
    protected $files = [];
    protected $aclFile;
    protected $dataDir;
    protected $rootDir;

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            if (!defined("ROOT_DIR")) {
                throw new \UnexpectedValueException("Root dir not defined");
            } else {
                $this->rootDir = ROOT_DIR;
            }
        }
        return $this->rootDir;
    }

    /**
     * @param mixed $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return mixed
     */
    public function getDataDir()
    {
        if (null === $this->dataDir) {
            if (defined("DATA_DIR")) {
                $this->dataDir = DATA_DIR;
            } else {
                $rootDir = $this->getRootDir();
                $dataDir = $rootDir . DIRECTORY_SEPARATOR . self::DATA_ROOT_DIR_NAME . DIRECTORY_SEPARATOR . static::DATA_DIR_NAME;
                if (!is_dir($dataDir)) {
                    $result = mkdir($dataDir, 0750, true);
                    if ($result) {
                        throw new \RuntimeException(sprintf('Could not create data dir "%s"', $dataDir));
                    }
                    $this->dataDir = $dataDir;
                }
            }
        }
        return $this->dataDir;
    }

    /**
     * @param mixed $dataDir
     */
    public function setDataDir($dataDir)
    {
        $this->dataDir = $dataDir;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function addFile($file)
    {
        $this->files[] = $file;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files)
    {
        $this->files = $files;
    }

    public function mergeFiles($files)
    {
        $fileName = "";
        foreach ($files as $file) {
            $files = (string) $file;
            if (is_readable($file)) {
                $fileName .= $file . filemtime($file);
            }
        }
        $fileName = "acl-xacl-" . md5($fileName);
        $filePath = $this->getDataDir() . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($filePath)) {
            $fullAcl = new File();
            foreach ($files as $file) {
                $confFile = new File($file);
                $fullAcl->merge($confFile);
            }
            $fullAcl->optimize();
            file_put_contents($filePath, (string)$fullAcl . "\n");
        }
        return $filePath;
    }

    /**
     * @return mixed
     */
    public function getAclFile()
    {
        if (null === $this->aclFile) {
            $this->aclFile = $this->mergeFiles($this->getFiles());
        }
        return $this->aclFile;
    }

    public function prepareResource($resource)
    {
        $resource = trim(trim($resource, "/"));
        $resource = strtr($resource, "/", ":");
        if ($resource === "") {
            $resource = self::ROOT_RESOURCE_PATH;
        }

        return $resource;
    }

    public function accessCheck($resource, GroupInterface $group, UserInterface $user = null, UserInterface $owner = null)
    {
        $resource = $this->prepareResource($resource);
        $params = [
            "-c" => $this->getAclFile(),
            "-g" => $group->getId(),
            "-r" => $resource,
            "-u" => ($user instanceof UserInterface) ? $user->getId() : null,
            "-o" => ($owner instanceof UserInterface) ? $owner->getId() : null,
        ];
        $params = ArrayTools::filterNulls($params);
        $params = ArrayTools::implodePairs(" ", $params, " ");
        $output = [];
        exec("x-acl " . $params, $output, $code);
        if ($code !== 0) {
            throw new \RuntimeException("Error in acl check. Code: $code. Msg: " . implode(" ", $output));
        }
        $output = reset($output);

        return $output === "Access allow";
    }
}
