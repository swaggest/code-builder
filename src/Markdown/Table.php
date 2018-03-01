<?php

namespace Swaggest\CodeBuilder\Markdown;

use Swaggest\CodeBuilder\AbstractTemplate;
use Swaggest\CodeBuilder\TableRenderer;

class Table extends AbstractTemplate
{
    private $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    protected function toString()
    {
        return (string)TableRenderer::create(new \ArrayIterator($this->rows))
            ->setColDelimiter('|')
            ->setHeadRowDelimiter('-')
            ->setOutlineVertical(true)
            ->setShowHeader();
    }
}