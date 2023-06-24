<?php


namespace Akademiano\Operator\WorkersMap\Filter;

use Akademiano\Delegating\Command\CommandInterface;

/**
 * used to filter worker relation based on command runtime values
 */
class Filter
{
    /** @var array|FieldFilter[] */
    protected $fieldsFilters;

    /**
     * Filter constructor.
     * @param array $fieldsFilters
     */
    public function __construct(array $fieldsFilters)
    {
        $this->fieldsFilters = $fieldsFilters;
    }

    public function filter(CommandInterface $command)
    {
        $f = 0;
        $l = 0;
        foreach ($this->fieldsFilters as $filter) {
            $f++;
            $extractor = $filter->getExtractor();
            $commandValueVariants = call_user_func($extractor, $filter->getName(), $command);
            $accept = false;
            foreach ($commandValueVariants as $valueVariant) {
                $l++;
                $assertion = $filter->getAssertion();
                if (is_callable($assertion)) {
                    $assertion = call_user_func($assertion, $filter->getName(), $command);
                }
                if (is_array($assertion)) {
                    foreach ($assertion as $assert) {
                        if ($assert === $valueVariant) {
                            $accept = true;
                            break;
                        }
                    }
                } else {
                    $accept = $assertion === $valueVariant;
                }
                if ($accept) {
                    break;
                }
            }
            if (!$accept) {
                return false;
            }
        }
        $effort = ($f > 0) ? ceil($l / $f) : 0;
        return $effort;
    }

    public function __invoke(CommandInterface $command)
    {
        return $this->filter($command);
    }
}
