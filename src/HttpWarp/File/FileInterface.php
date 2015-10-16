<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace HttpWarp\File;


interface FileInterface
{
    public function getPath();

    public function getSize();

    public function getType();

    public function mv($path);

    public function checkType($type);

    public function isImage();

    public function getExt();

}
