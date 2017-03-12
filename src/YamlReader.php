<?php

namespace Akademiano\Utils;


use Symfony\Component\Yaml\Parser;

class YamlReader
{
    /** @var  Parser */
    protected static $parser;

    public static function getParser()
    {
        if (null === static::$parser) {
            static::$parser = new Parser();
        }
        return static::$parser;
    }

    public static function parseFile($file)
    {
        return static::getParser()->parse(file_get_contents($file));
    }

}
