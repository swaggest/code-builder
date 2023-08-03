<?php

namespace Swaggest\CodeBuilder\Tests;


use Swaggest\CodeBuilder\TableRenderer;

class TableRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testTableWithMultilineCellDelimiter()
    {
        $rows = new \ArrayIterator(array(
            [
                'Col1' => "abc",
                'Col2' => 'bla bla bla',
                'Description' => "longer line with a few\nline breaks\nand other things\nfin!",
            ],
            [
                'Col1' => "def",
                'Col2' => 'bla bla',
                'Description' => "pf",
            ]
        ));

        $tr = new TableRenderer($rows);
        $tr->multilineCellDelimiter('<br>');

        $this->assertEquals('abc   bla bla bla   longer line with a few<br>line breaks<br>and other things<br>fin!
def   bla bla       pf', trim($tr->__toString()));

    }

}