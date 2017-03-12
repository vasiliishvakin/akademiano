<?php

class ArrayToolsTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    // tests
    public function testMe()
    {
        $array = ["a1" => ["b1"=>1]];
        $this->assertEquals(1, \Akademiano\Utils\ArrayTools::get($array, ["a1", "b1"]));
        $this->assertEquals($array, \Akademiano\Utils\ArrayTools::get($array, null));
    }
}
