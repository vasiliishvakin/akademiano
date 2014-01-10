<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace OrbisTools;


class YamlReader 
{
    public static function parseFile($file)
    {
        return yaml_parse_file($file);
    }

} 