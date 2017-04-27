<?php

namespace Swaggest\CodeBuilder;

class PlaceholderString extends AbstractTemplate
{
    /** @var string */
    private $code;
    /** @var AbstractTemplate[]|array */
    private $binds;

    /**
     * CodeTemplate constructor.
     * @param string $code
     * @param array|\Swaggest\CodeBuilder\AbstractTemplate[] $binds
     */
    public function __construct($code, array $binds)
    {
        $this->code = $code;
        $this->binds = $binds;
    }


    protected function toString()
    {
        $replace = array();
        foreach ($this->binds as $name => $value) {
            $replace[$name] = (string)$value;
        }
        return strtr($this->code, $replace);
    }
}