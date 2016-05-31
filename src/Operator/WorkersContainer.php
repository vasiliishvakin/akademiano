<?php


namespace EntityOperator\Operator;


use Pimple\Container;

class WorkersContainer extends Container implements IncludeOperatorInterface
{
    use IncludeOperatorTrait;

    public function offsetSet($id, $value)
    {
        if (!is_object($value) || !method_exists($value, '__invoke')) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" does not contain an object definition.', $id));
        }

        $value = function ($c) use ($value) {
            if (is_callable($value)) {
                $result = $value($c);
                if (is_object($result) && $result instanceof IncludeOperatorInterface) {
                    $result->setOperator($this->getOperator());
                }
                return $result;
            }else {
                return $value;
            }
        };
        parent::offsetSet($id, $value);
    }
}
