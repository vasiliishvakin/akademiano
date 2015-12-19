<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model\Adapter;


use DeltaUtils\ArrayUtils;
use DeltaUtils\StringUtils;

class XAclAdapter extends AbstractAdapter
{
    const ROOT_RESOURCE_PATH = "__root__";
    protected $aclFile;

    public function mergeFiles($files)
    {
        $fileName = "";
        foreach ($files as $file) {
            if (is_readable($file)) {
                $fileName .= $file . filemtime($file);
            }
        }
        $fileName = "acl_" . md5($fileName);
        $filePath = DATA_DIR . "/acl/" . $fileName;
        if (!file_exists(DATA_DIR . "/acl/")) {
            mkdir(DATA_DIR . "/acl/", 0750, true);
        }
        if (!file_exists($filePath)) {
            $acl = "";
            foreach($files as $file) {
                if (is_readable($file)) {
                    $fileData = file_get_contents($file);
                    $acl .= "\n" . $fileData;
                }
            }
            file_put_contents($filePath, $acl . "\n");
        }
        return $filePath;
    }

    /**
     * @return mixed
     */
    public function getAclFile()
    {
        if (null === $this->aclFile) {
            $files = $this->getConfig()->get(["Acl", StringUtils::cutClassName(__CLASS__), "file"], [
                ROOT_DIR . "/App/config/acl.conf",
                ROOT_DIR . "/config/acl.conf",
            ])->toArray();
            if (is_array($files)) {
                $this->aclFile = $this->mergeFiles($files);
            }
        }
        return $this->aclFile;
    }

    /**
     * @param mixed $aclFile
     */
    public function setAclFile(array $aclFile)
    {
        $this->aclFile = $aclFile;
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

    public function isAllow($group, $resource, $user = null, $owner = null)
    {
        $resource = $this->prepareResource($resource);
        $params = [
            "-c" => $this->getAclFile(),
            "-g" => $group,
            "-r" => $resource,
            "-u" => $user,
            "-o" => $owner,
        ];
        $params = ArrayUtils::filterNulls($params);
        $params = ArrayUtils::implodePairs(" ", $params, " ");
        $output = [];
        exec("x-acl " . $params, $output, $code);
        if ($code !== 0) {
            throw new \RuntimeException("Error in acl check. Code: $code. Msg: " . implode(" ", $output));
        }
        $output = reset($output);

        return $output === "Access allow";
    }

} 