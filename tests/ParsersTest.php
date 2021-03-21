<?php

namespace Egg\Tests;

use PHPUnit\Framework\TestCase;

use function Egg\Parsers\getString;
use function Egg\Parsers\getNumber;
use function Egg\Parsers\getWord;

class ParsersTest extends TestCase
{
    public function testGetString()
    {
          $this->assertNull(getString(""));
          $this->assertNull(getString("("));
          $this->assertNull(getString("+(a, 10"));
          $this->assertEquals(["\"()\"", "()"], getString("\"()\""));
    }

    public function testGetNumber()
    {
          $this->assertNull(getNumber(""));
          $this->assertNull(getNumber("+"));
          $this->assertNull(getNumber("+(a, 10"));
          $this->assertEquals(["9"], getNumber("9"));
          $this->assertEquals(["9"], getNumber(9));
    }

    public function testGetWord()
    {
          $this->assertNull(getWord(" "));
          $this->assertNull(getWord("("));
          $this->assertEquals(["+"], getWord("+(a, 10"));
          $this->assertEquals(["define"], getWord("define"));
          $this->assertEquals(["9"], getWord("9"));
          $this->assertEquals(["9"], getWord(9));
          $this->assertEquals(["pri"], getWord("pri nt"));
    }
}
