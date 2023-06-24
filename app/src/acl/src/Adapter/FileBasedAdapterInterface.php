<?php

namespace Akademiano\Acl\Adapter;


interface FileBasedAdapterInterface
{
    const CONFIG_NAME = "acl";
    const FILE_EXT = "ini";

    public function setFiles(array $files);

    public function getFiles();
}
