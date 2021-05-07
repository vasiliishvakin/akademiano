<?php


namespace Akademiano\LazyProperty;


use Akademiano\Utils\Exception;
use Ds\Hashable;

class Helper
{
    static private function clearMethod(string $methodName, ?string &$className = null): string
    {
        $className = null;
        if (false !== $classDelimiterPos = strpos($methodName, "::")) {
            $className = substr($methodName, 0, $classDelimiterPos);
            $methodOnlyName = substr($methodName, $classDelimiterPos + 2);
        }
        return $methodOnlyName ?? $methodName;
    }

    static private function methodCleared2Property(string $methodName)
    {
        $prefix = substr($methodName, 0, 3);
        switch ($prefix) {
            case "get":
            case "set":
            case "has":
                $propertyName = lcfirst(substr($methodName, 3));
                break;
            default:
                $prefix = substr($methodName, 0, 2);
                switch ($prefix) {
                    case "is":
                        $propertyName = lcfirst(substr($methodName, 2));
                        break;
                    default:
                        throw new \OutOfRangeException(sprintf("Only work with get method prefix, given \"%s\" with prefix \"%s\".", $methodName, $prefix));
                }
        }
        return $propertyName;
    }

    static public function method2Property(string $methodName): string
    {
        $clearedMethod = self::clearMethod($methodName);
        return self::methodCleared2Property($clearedMethod);
    }

    static public function getObjectHash(object $object): string
    {
        $hash = null;
        if ($object instanceof Hashable) {
            try {
                $hash = $object->hash();
            } catch (\Exception $e) {
                $hash = (string)spl_object_id($object);
            }
        } else {
            $hash = (string)spl_object_id($object);
        }
        return $hash;
    }

    static public function propertyId(string $objectHash, string $methodName): string
    {
        $clearedMethod = self::clearMethod($methodName, $className);
        if (empty($className)) {
            throw new \InvalidArgumentException(sprintf("Method signature \"%s\" not contain class", $methodName));
        }
        $property = self::methodCleared2Property($clearedMethod);

        return sprintf("%s::%s::%s", $className, $objectHash, $property);
    }
}
