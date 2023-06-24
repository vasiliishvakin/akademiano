<?php


namespace Akademiano\Utils\Parts;


trait InheritClassConstantsTrait
{
    protected static function getClassTreeArrayConstant(string $constantName): ?array
    {
        $parenClass = get_parent_class(static::class);
        $parentFields = null;
        if ($parenClass) {
            $parentFields = call_user_func([$parenClass, __FUNCTION__], $constantName);
        }
        $currentFields = constant(static::class . '::' . $constantName);
        $mergedFields = $parentFields ? array_merge($parentFields, $currentFields) : $currentFields;
        return $mergedFields;
    }
}