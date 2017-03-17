<?php

namespace Akademiano\HttpWarp\File;


interface FileInterface
{
    public function getPath();

    public function getSize();

    public function getMimeType();

    public function getType();

    public function getSubType();

    public function mv($path);

    public function checkType($type);

    public function isImage();

    public function getExt();

}
