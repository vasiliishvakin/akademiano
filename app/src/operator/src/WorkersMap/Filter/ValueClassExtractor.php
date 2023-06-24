<?php


namespace Akademiano\Operator\WorkersMap\Filter;


use Akademiano\Delegating\Command\CommandInterface;

class ValueClassExtractor extends ValueExtractor
{
    public function extract(string $fieldName,  CommandInterface $command): \Traversable
    {
        $methodsPrefix = ['get', 'is'];
        foreach ($methodsPrefix as $prefix) {
            $method = $prefix . ucfirst($fieldName);
            if (method_exists($command, $method)) {
                $valueData = call_user_func([$command, $method]);
                if (is_object($valueData)) {
                    $value = get_class($valueData);
                    break;
                }
            }
        }
        if (!isset($value)) {
            return;
        } else {
            yield $value;
        }
    }
}
