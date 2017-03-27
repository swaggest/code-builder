<?php

namespace Swaggest\CodeBuilder;


class ClosureString extends AbstractTemplate
{
    /** @var \Closure */
    private $closure;

    /**
     * ClosureString constructor.
     * @param $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function toString()
    {
        try {
            $result = $this->closure->__invoke();
            if ($result === null) {
                return '';
            }
            return $result;
        } catch (\Exception $exception) {
            return $exception->getMessage() . "\n" . $exception->getTraceAsString();
        }
    }
}