<?php


namespace DeltaUtils;


use Symfony\Component\Yaml\Dumper;

class YamlWriter
{
    /** @var  Dumper */
    static $dumper;

    public static function getDumper()
    {
        if (null === self::$dumper){
            self::$dumper = new Dumper();
        }
        return self::$dumper;
    }

    public static function emit($data, $inline = 0)
    {
        return self::getDumper()->dump($data, $inline);
    }

    public static function emitFile($data, $fileName, $inline = 0)
    {
        $data = self::emit($data, $inline);
        return file_put_contents($fileName, $data);
    }
}
