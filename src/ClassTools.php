<?php


namespace Akademiano\Utils;


class ClassTools
{
    public static function getClassTreeConstants(string $class, string $constantName): ?array
    {
        $result = [];
        if ($parent = get_parent_class($class)) {
            $result = $result + self::getClassTreeConstants($parent, $constantName);
        }

        $fullConstantName = $class . '::' . $constantName;
        if (defined($fullConstantName)) {
            $result[$class] = constant($fullConstantName);
        }
        return $result;
    }

    public static function getClassTreeArrayConstant(string $class, string $constantName): ?array
    {
        $constants = self::getClassTreeConstants($class, $constantName);
        $constants = array_reverse($constants);
        $constants = call_user_func_array('array_merge', $constants);
        $constants = array_unique($constants);
        return $constants;
    }

}
