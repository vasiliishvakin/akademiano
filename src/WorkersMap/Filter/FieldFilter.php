<?php


namespace Akademiano\Operator\WorkersMap\Filter;


class FieldFilter implements FilterFieldInterface
{
    /** @var string */
    protected $name;

    /** @var mixed */
    protected $assertion;

    /** @var \Closure */
    protected $extractor;

    /**
     * FieldFilter constructor.
     * @param string $name
     * @param string $assertion
     * @param \Closure $extractor
     */
    public function __construct(string $name, $assertion, \Closure $extractor = null)
    {
        $this->name = $name;
        $this->assertion = $assertion;
        $this->extractor = $extractor;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAssertion()
    {
        return $this->assertion;
    }

    /**
     * @return \Closure
     */
    public function getExtractor(): \Closure
    {
        if (null === $this->extractor) {
            $this->extractor = \Closure::fromCallable([ValueExtractor::class, 'extract']);
        }
        return $this->extractor;
    }
}
