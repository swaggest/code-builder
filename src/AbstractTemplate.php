<?php

namespace Swaggest\CodeBuilder;

abstract class AbstractTemplate
{
    abstract protected function toString();

    private $preparedOnce = false;
    protected function prepareOnce()
    {
    }

    protected function prepare()
    {
    }

    public function __toString()
    {
        if (!$this->preparedOnce) {
            $this->prepareOnce();
            $this->preparedOnce = true;
        }
        $this->prepare();

        return $this->toString();
    }


    public function padLines($with, $text, $skipFirst = true)
    {
        $lines = explode("\n", $text);
        foreach ($lines as $index => $line) {
            if ($skipFirst && !$index) {
                continue;
            }
            if ($line) {
                $lines[$index] = $with . $line;
            }
        }
        return implode("\n", $lines);
    }

    public function trimLines($text)
    {
        $lines = explode("\n", $text);
        foreach ($lines as $index => $line) {
            if ($line) {
                $lines[$index] = trim($line);
            }
        }
        return implode("\n", $lines);
    }

}