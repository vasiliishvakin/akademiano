<?php
/**
 * User: Vasiliy Shvakin (aka orbisnull)
 */

namespace DeltaUtils;

use DeltaUtils\Object\File;

class FileSystem
{
    const FST_ALL = 'all';
    const FST_DIR = 'dir';
    const FST_FILE = 'file';
    const FST_IMAGE = 'image';

    const LIST_OBJ = 'object';
    const LIST_SCALAR = 'scalar';

    public static function getFileType($path)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $info = $finfo->file($path);
        return $info;
    }

    public static function isWebImage($path)
    {
        $fileType = self::getFileType($path);
        switch ($fileType) {
            case 'image/jpeg' :
                return 'jpg';
            case 'image/gif' :
                return 'gif';
            case 'image/png' :
                return 'png';
            default :
                return false;
        }
    }

    public static function isSpecDir($dir)
    {
        return ($dir === "." || $dir === "..");
    }

    public static function isHidden($name)
    {
        return strpos($name, '.') === 0;
    }

    public static function checkType($path, $type)
    {
        switch ($type) {
            case self::FST_ALL :
                return true;
                break;
            case self::FST_DIR :
                return is_dir($path);
                break;
            case self::FST_FILE :
                return is_file($path);
                break;
            case self::FST_IMAGE :
                return self::isWebImage($path);
                break;
            default :
                throw new \InvalidArgumentException("check function for type $type not defined");
        }
    }

    public static function getItems($path, $resultType=self::LIST_SCALAR, $itemType = self::FST_ALL, $level = false, $showHidden = false)
    {
        if (!$path) {
            return null;
        }
        if ($level!==false && $level<=0) {
            return null;
        }
        //если это папка и рекурсивно или левел не 0 - идем в рекурсию
        if ($level !== false) {
            $level--;
        }

        $items = [];
        if ($handle = opendir($path)) {
            while (false !== ($item = readdir($handle))) {
                $itemPath = $path . DIRECTORY_SEPARATOR . $item;
                if (self::isSpecDir($item)) {
                    continue;
                }
                if (!$showHidden && self::isHidden($item)) {
                    continue;
                }
                if (($level === false || $level>0) && is_dir($itemPath)) {
                    $itemsInFolder = self::getItems($itemPath, $resultType, $itemType, $level, $showHidden);
                    $items[$item] = $itemsInFolder;
                }
                //проверяем тип
                if (!self::checkType($itemPath, $itemType)) {
                    continue;
                }
                if ($resultType === self::LIST_OBJ) {
                    $items[] = new File($itemPath, $item);
                } else {
                    $items[] = $item;
                }
            }
            closedir($handle);
        }
        return $items;
    }

    public static function getDirs($path, $resultType=self::LIST_SCALAR, $level = 1, $showHidden = false)
    {
        return self::getItems($path, $resultType, self::FST_DIR, $level, $showHidden);
    }

    public static function getFiles($path, $resultType=self::LIST_SCALAR, $level = 1, $showHidden = false)
    {
        return self::getItems($path, $resultType, self::FST_FILE, $level, $showHidden);
    }

    public static function inDir($parentDir, $dir, $checkRealPath = true)
    {
        if ($checkRealPath) {
            $parentDir = realpath($parentDir);
            $dir = realpath($dir);
        }
        if (strlen($parentDir) >  strlen($dir)) {
            return false;
        }
        $partDir = substr($dir, 0, strlen($parentDir));
        if ((!$parentDir || !$dir || !$partDir) || ($parentDir !== $partDir)) {
            return false;
        }
        return $dir;
    }

    public static function getDirName($path, $level = 1)
    {
        for($i = 1; $i<=$level; $i++) {
            $path = dirname($path);
        }
        return $path;
    }

}