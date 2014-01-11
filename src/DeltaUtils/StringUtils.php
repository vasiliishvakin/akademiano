<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaUtils;


class StringUtils 
{
    public static function cropBySpace($str, $size){
        return mb_substr($str, 0, mb_strrpos(mb_substr($str, 0, $size), ' '));
    }
} 