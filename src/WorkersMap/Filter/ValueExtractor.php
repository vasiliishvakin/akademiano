<?php


namespace Akademiano\Operator\WorkersMap\Filter;


use Akademiano\Delegating\Command\CommandInterface;

class ValueExtractor
{
    public function extract(string $fieldName,  CommandInterface $command): \Traversable
    {
        $methodsPrefix = ['get', 'is'];
        foreach ($methodsPrefix as $prefix) {
            $method = $prefix . ucfirst($fieldName);
            if (method_exists($command, $method)) {
                $value = call_user_func([$command, $method]);
                break;
            }
        }
        if (!isset($value)) {
            return;
        } else {
            yield $value;
        }
    }

    public function __invoke(string $fieldName,  CommandInterface $command): \Traversable
    {
        return $this->extract($fieldName, $command);
    }
}
