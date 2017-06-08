<?php


namespace Akademiano\Operator\Worker;


trait WorkerMetaMapPropertiesTrait
{
    protected static function getDefaultMetadata()
    {
        return [];
    }

    protected static function getDefaultMapping()
    {
        return [];
    }

    public static function getMetadata(array $metadata = null, $replace = false)
    {

        $defaultMetadata = static::getDefaultMetadata();
        if (null === $metadata) {
            return $defaultMetadata;
        }
        if ($replace) {
            return $metadata;
        } else {
            return array_merge($defaultMetadata, $metadata);
        }
    }

    public static function mergeMapping(array $oldMapping, $mapping, $replace = false)
    {
        if (null === $mapping) {
            return $oldMapping;
        }
        if (is_array($mapping)) {
            if ($replace) {
                return $mapping;
            } else {
                return array_merge($oldMapping, $mapping);
            }
        } else {
            $mapping = (string)$mapping;
            $newMapping = [];
            foreach ($oldMapping as $action => $class) {
                $newMapping[$action] = $mapping;
            }
            return $newMapping;
        }
    }

    public static function getMapping($mapping = null, $replace = false)
    {
        $defaultMapping = static::getDefaultMapping();
        if (null === $mapping) {
            return $defaultMapping;
        }
        return static::mergeMapping($defaultMapping, $mapping, $replace);
    }
}
