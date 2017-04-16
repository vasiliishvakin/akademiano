<?php

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Akademiano\HttpWarp\Request */
    protected $request;

    protected function setUp()
    {
        $this->request = new \Akademiano\HttpWarp\Request();
    }

    protected function tearDown()
    {
        unset($this->request);
    }

    // tests
    public function testGetMethod()
    {
        $method = "GET";
        $_SERVER["REQUEST_METHOD"] = $method;
        $this->assertEquals($method, $this->request->getMethod());
        $this->assertNotEquals("POST", $this->request->getMethod());
    }


    public function testGetParams()
    {
        $method = "GET";
        $_SERVER["REQUEST_METHOD"] = $method;
        $params = ["test" => "test"];
        $_GET = $params;


        $this->assertEquals($params, $this->request->getParams());
        $this->assertEquals($params["test"], $this->request->getParam("test"));
        $this->assertNotEquals("", $this->request->getParam("test"));

        $this->assertNull($this->request->getParam("notExist"));
    }

}
