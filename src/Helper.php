<?php


namespace Akademiano\LazyProperty;


class Helper
{
    static private function clearMethod(string $methodName, ?string &$className): string
    {
        $className = null;
        if ($classDelimiterPos = strpos($methodName, "::") !== false) {
            $className = substr($methodName, 0, $classDelimiterPos);
            $methodOnlyName = substr($methodName, $classDelimiterPos + 2);
        }
        return  $methodOnlyName ?? $methodName;
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
        return  self::methodCleared2Property($clearedMethod);
    }

    static public function propertyId(string $methodName): string
    {
        $clearedMethod = self::clearMethod($methodName, $className);
        if (empty($className)) {
            throw new \InvalidArgumentException(sprintf("Method signature \"%s\" not contain class", $methodName));
        }
        $property = self::methodCleared2Property($clearedMethod);

        return sprintf("%s::%s", $className, $property);
    }
}
