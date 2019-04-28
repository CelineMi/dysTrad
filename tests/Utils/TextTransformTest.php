<?php

namespace App\Tests\Utils;

use App\Utils\TextTransform;
use PHPUnit\Framework\TestCase;

class TextTransformTest extends TestCase
{
    public function testTextToLine()
    {
        $helper = new TextTransform();
        $actual = $helper->textToLine('salut ça va bien ?', 9);
        $expected = [
            ['salut', 'ça'],
            ['va', 'bien', '?']
        ];

       $this->assertEquals($actual, $expected);

    }

    function testTextToLineTextEqualLenLine()
    {
        $helper = new TextTransform();
        $actual = $helper->textToLine('aaaaaaaaa aaaaaaaaa', 9);
        $expected = [
            ['aaaaaaaaa'],
            ['aaaaaaaaa']
        ];

        $this->assertEquals($actual, $expected);
    }
}