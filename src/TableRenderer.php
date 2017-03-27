<?php

namespace Swaggest\CodeBuilder;

use Yaoi\Cli\View\Text;
use Yaoi\View\Hardcoded;
use Yaoi\Io\Content\Renderer;
use Yaoi\Io\Content;

class TableRenderer extends Hardcoded implements Renderer
{
    private $colDelimiter = '   ';
    private $rowDelimiter = null;
    /** @var \Iterator */
    private $rowsIterator;
    private $headRowDelimiter;
    private $headColDelimiter;
    private $outlineVertical;

    public function __construct(\Iterator $rows)
    {
        $this->rowsIterator = $rows;
    }

    public function setColDelimiter($delimiter = '   ')
    {
        $this->colDelimiter = $delimiter;
        return $this;
    }

    public function setRowDelimiter($delimiter = null)
    {
        $this->rowDelimiter = $delimiter;
        return $this;
    }

    private $showHeader = false;

    public function setShowHeader($yes = true)
    {
        $this->showHeader = $yes;
        return $this;
    }

    /**
     * @param mixed $headRowDelimiter
     * @return TableRenderer
     */
    public function setHeadRowDelimiter($headRowDelimiter)
    {
        $this->headRowDelimiter = $headRowDelimiter;
        return $this;
    }

    /**
     * @param mixed $headColDelimiter
     * @return TableRenderer
     */
    public function setHeadColDelimiter($headColDelimiter)
    {
        $this->headColDelimiter = $headColDelimiter;
        return $this;
    }

    /**
     * @param mixed $outlineVertical
     * @return TableRenderer
     */
    public function setOutlineVertical($outlineVertical)
    {
        $this->outlineVertical = $outlineVertical;
        return $this;
    }


    private $lines = array();
    private $length = array();
    private $keys = array();
    private $rows = array();
    private $rowDelimiterLine;

    private function findLines()
    {
        foreach ($this->rows as $rowIndex => $row) {
            foreach ($row as $key => $value) {
                if (!$value instanceof Content\Text) {
                    $value = new Content\Text($value);
                }
                $renderer = new Text($value);
                foreach ($renderer->lines() as $lineIndex => $line) {
                    $stringLength = strlen($line->text->value);
                    if (!isset($this->length[$key]) || $this->length[$key] < $stringLength) {
                        $this->length[$key] = $stringLength;
                    }
                    $this->lines [$rowIndex][$lineIndex][$key] = $line;
                }
            }
        }
    }

    private function findKeys()
    {
        $this->keys = array();

        $this->rows = array(0 => array());
        foreach ($this->rowsIterator as $rowIndex => $row) {
            $this->rows [] = $row;

            foreach ($row as $key => $value) {
                $this->keys [$key] = $key;
            }
        }
        if ($this->showHeader) {
            $this->rows[0] = $this->keys;
        }
    }

    public function echoLines()
    {
        foreach ($this->lines as $rowIndex => $rowData) {
            foreach ($rowData as $lineIndex => $row) {
                $line = $this->buildLine($row);
                echo $line, PHP_EOL;

                if ($this->headRowDelimiter && $this->showHeader && (0 === $rowIndex)) {
                    echo $this->getRowDelimiter($this->headRowDelimiter), PHP_EOL;

                } elseif ($this->rowDelimiter) {
                    if (null === $this->rowDelimiterLine) {
                        $this->rowDelimiterLine = $this->getRowDelimiter($this->rowDelimiter);
                    }
                    echo $this->rowDelimiterLine, PHP_EOL;
                }
            }
        }
    }

    protected function buildLine($row, $pad = ' ')
    {
        $line = '';
        foreach ($this->length as $key => $maxLength) {
            /** @var \Yaoi\Cli\View\Text $value */
            $value = isset($row[$key]) ? $row[$key] : null;

            if ($line) {
                $line .= $this->colDelimiter;
            }
            if ($value) {
                $value->strPad($maxLength, $pad);
                $line .= $value->text->value;
            } else {
                $line .= str_repeat($pad, $maxLength);
            }
        }
        if ($this->outlineVertical) {
            $line = $this->colDelimiter . $line . $this->colDelimiter;
        }
        return $line;
    }

    protected function getRowDelimiter($delimiter)
    {
        return $this->buildLine(array(), $delimiter);
    }

    public function render()
    {
        $this->rowDelimiterLine = null;
        $this->findKeys();
        $this->findLines();
        $this->echoLines();
    }
}