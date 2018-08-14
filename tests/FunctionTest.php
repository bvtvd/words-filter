<?php

namespace Test;

//require_once __DIR__ . '/../vendor/autoload.php';

use bvtvd\Filter;
use PHPUnit\Framework\TestCase;


class FunctionTest extends TestCase
{
    public function testClean()
    {
        $test = '你好';
        $dict = ['你好'];
        $filter = new Filter($test, $dict);

        $this->assertEquals('**', $filter->clean());
    }
}
