<?php

namespace Swaggest\CodeBuilder;


use Yaoi\BaseClass;

class StackTraceStorage extends BaseClass
{
    private $inclusive = array();
    private $exclusive = array();

    public function addData($value)
    {
        if (count($this->exclusive) === 0) {
            throw new \Exception();
        }
        foreach ($this->inclusive as &$frame) {
            $frame[] = $value;
        }
        $this->exclusive[count($this->exclusive) - 1][] = $value;
        return $this;
    }

    public function addUnique($key, $value)
    {
        foreach ($this->inclusive as &$frame) {
            $frame[$key] = $value;
        }
        $this->exclusive[count($this->exclusive) - 1][$key] = $value;

        return $this;
    }

    public function push()
    {
        $this->inclusive[] = array();
        $this->exclusive[] = array();
        return $this;
    }


    public function pop($inclusive = false)
    {
        $inclusiveFrame = array_pop($this->inclusive);
        $exclusiveFrame = array_pop($this->exclusive);
        return $inclusive ? $inclusiveFrame : $exclusiveFrame;
    }

}