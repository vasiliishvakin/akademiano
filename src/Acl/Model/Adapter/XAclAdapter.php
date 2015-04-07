<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model\Adapter;


use DeltaUtils\ArrayUtils;

class XAclAdapter extends AbstractAdapter
{
    const ROOT_RESOURCE_PATH = "__root__";
    protected $aclFile;

    /**
     * @return mixed
     */
    public function getAclFile()
    {
        if (is_null($this->aclFile)) {
            $file = $this->getConfig()->get(["Acl", __CLASS__, "file"], ROOT_DIR . "/config/acl.conf");
            if (!is_readable($file)) {
                throw new \RuntimeException("Acl config file $file not readable");
            }
            $this->aclFile = $file;
        }
        return $this->aclFile;
    }

    /**
     * @param mixed $aclFile
     */
    public function setAclFile($aclFile)
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
            "-g"    => $group,
            "-r" => $resource,
            "-u"  => $user,
            "-o" => $owner,
        ];
        $params = ArrayUtils::filterNulls($params);
        $params = ArrayUtils::implodePairs(" ", $params, " ");
        $output = [];
        exec("x-acl " . $params, $output, $code);
        if ($code !==0 ) {
            throw new \RuntimeException("Error in acl check. Code: $code. Msg: " . implode(" ", $output));
        }
        $output = reset($output);
        return $output === "Access allow";
    }

} 