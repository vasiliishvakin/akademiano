<?php


namespace Akademiano\Utils;


use Symfony\Component\Yaml\Dumper;

class YamlWriter
{
    /** @var  Dumper */
    protected static $dumper;

    public static function getDumper()
    {
        if (null === static::$dumper){
            static::$dumper = new Dumper();
        }
        return static::$dumper;
    }

    public static function emit($data, $inline = 0)
    {
        return static::getDumper()->dump($data, $inline);
    }

    public static function emitFile($data, $fileName, $inline = 0)
    {
        $data = static::emit($data, $inline);
        return file_put_contents($fileName, $data);
    }
}
