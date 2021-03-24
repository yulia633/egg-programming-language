<?php

namespace Egg\Tests;

use PHPUnit\Framework\TestCase;

use function Egg\Parsers\getString;
use function Egg\Parsers\getNumber;
use function Egg\Parsers\getWord;
use function Egg\Parsers\parse;

class ParsersTest extends TestCase
{
    public function testGetString()
    {
          $this->assertEquals("()", "()", getString("\"()\""));
    }

    public function testGetNumber()
    {
          $this->assertEquals(0, getNumber("text"));
          $this->assertEquals(1, getNumber("9"));
          $this->assertEquals(1, getNumber(9));
    }

    public function testGetWord()
    {
          $this->assertEquals(["+"], getWord("+(a, 10"));
          $this->assertEquals(["define"], getWord("define"));
          $this->assertEquals(["9"], getWord("9"));
          $this->assertEquals(["9"], getWord(9));
          $this->assertEquals(["pri"], getWord("pri nt"));
    }

    public function testParse()
    {
          $expected = [
            'type' => 'apply',
            'operator' => [
                  'type' => 'word',
                  'name' => '+'
            ],
            'args' => [
                  [
                        'type' => 'word',
                        'name' => 'a'
                  ],
                  [
                        'type' => 'value',
                        'value' => '10'
                  ]
            ],
          ];
          $this->assertEquals($expected, parse("   +(a, 10)"));
    }
}
