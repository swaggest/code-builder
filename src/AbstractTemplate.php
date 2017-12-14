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

    /**
     * @return string
     */
    public function render()
    {
        if (!$this->preparedOnce) {
            $this->prepareOnce();
            $this->preparedOnce = true;
        }
        $this->prepare();

        return $this->toString();
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\ErrorException $e) {
            return $e->getTraceAsString();
        } catch (\Exception $e) {
            return $e->getTraceAsString();
        }
    }


    public function padLines($with, $text, $skipFirst = true, $forcePad = false)
    {
        $lines = explode("\n", $text);
        foreach ($lines as $index => $line) {
            if ($skipFirst && !$index) {
                continue;
            }
            if ($line || $forcePad) {
                $lines[$index] = $with . $line;
            }
        }
        return implode("\n", $lines);
    }

    public function indentLines($text)
    {
        return $this->padLines("\t", $text, false);
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