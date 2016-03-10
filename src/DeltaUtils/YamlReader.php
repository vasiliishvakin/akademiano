<?php

namespace DeltaUtils;


use Symfony\Component\Yaml\Parser;

class YamlReader
{
    /** @var  Parser */
    static $parser;

    public static function getParser()
    {
        if (null === self::$parser) {
            self::$parser = new Parser();
        }
        return self::$parser;
    }

    public static function parseFile($file)
    {
        return self::getParser()->parse(file_get_contents($file));
    }

}
