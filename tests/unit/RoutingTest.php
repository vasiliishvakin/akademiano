<?php

use Akademiano\HttpWarp\Url;
use Akademiano\Router\RoutePattern;
use Akademiano\Router\Route;
use Akademiano\Router\Router;


class RoutingTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  Url */
    protected $url;
    /** @var  Router */
    protected $router;

    /** @evar  Request */
    protected $request;

    protected function _before()
    {
        $this->router = new Router();
        $this->url = new Url();
        $this->request = \Mockery::mock("\\Akademiano\\HttpWarp\\Request",
            [
                "getMethod" => "GET",
            ])->makePartial();
        $this->request->setUrl($this->url);
        $this->router->setRequest($this->request);
    }

    protected function _after()
    {
        unset($this->router);
        unset($this->request);
        unset($this->url);
    }

    // tests
    public function testHost()
    {
        $route = [
            "methods" => [Route::METHOD_ALL],
            "patterns" => [
                "part" => RoutePattern::PART_DOMAIN,
                "type" => RoutePattern::TYPE_FULL,
                "value" => "test.test",
            ],
            "action" => function () {
                return "OK";
            }
        ];
        $this->router->setRoutes([$route]);

        $this->url->setDomain("test.test");
        $this->assertEquals("OK", $this->router->run());
    }

    /**
     * @expectedException \Akademiano\HttpWarp\Exception\NotFoundException
     */
    public function testNotRouted()
    {
        $route = [
            "methods" => [Route::METHOD_ALL],
            "patterns" => [
                "part" => RoutePattern::PART_DOMAIN,
                "type" => RoutePattern::TYPE_FULL,
                "value" => "test.test",
            ],
            "action" => function () {
                return "OK";
            }
        ];
        $this->router->setRoutes([$route]);

        $this->url->setDomain("superhostt");
        $this->assertEquals("OK", $this->router->run());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEmpty()
    {
        $this->router->setRoutes([]);
        $this->router->run();
    }

    public function testPathFull()
    {
        $route = [
            "methods" => [Route::METHOD_GET],
            "patterns" => [
                "part" => RoutePattern::PART_PATH,
                "type" => RoutePattern::TYPE_FULL,
                "value" => "/home",
            ],
            "action" => function () {
                return "OK";
            }
        ];
        $this->router->setRoutes([$route]);

        $this->url->setPath("/home");
        $this->assertEquals("OK", $this->router->run());

        $this->url->setPath("/home/");
        $this->assertEquals("OK", $this->router->run());
    }

    public function testParams()
    {
        $route = [
            "methods" => [Route::METHOD_GET],
            "patterns" => [
                "part" => RoutePattern::PART_QUERY,
                "type" => RoutePattern::TYPE_PARAMS,
                "value" => ["id" => 4],
            ],
            "action" => function () {
                return "OK";
            }
        ];
        $this->router->setRoutes([$route]);

        $this->url->setQuery("id=4&test=www");
        $this->assertEquals("OK", $this->router->run());
    }

    public function testComplex()
    {
        $route = [
            "methods" => [Route::METHOD_GET],
            "patterns" => [
                [
                    "part" => RoutePattern::PART_PATH,
                    "type" => RoutePattern::TYPE_FULL,
                    "value" => "/home",
                ],
                [
                    "part" => RoutePattern::PART_DOMAIN,
                    "type" => RoutePattern::TYPE_FULL,
                    "value" => "test.test",
                ]
            ],
            "action" => function () {
                return "OK";
            }
        ];
        $this->router->setRoutes([$route]);

        $this->url->setDomain("test.test");
        $this->url->setPath("/home/");
        $this->url->setQuery("id=4&test=www");
        $this->assertEquals("OK", $this->router->run());
    }

    public function testMany()
    {
        $routes = [
            [
                "methods" => [Route::METHOD_ALL],
                "patterns" => [
                    "part" => RoutePattern::PART_PATH,
                    "type" => RoutePattern::TYPE_FULL,
                    "value" => "/",
                ],
                "action" => function () {
                    return "/";
                }
            ],
            [
                "methods" => [Route::METHOD_ALL],
                "patterns" => [
                    "part" => RoutePattern::PART_PATH,
                    "type" => RoutePattern::TYPE_PREFIX,
                    "value" => "/home/id",
                ],
                "action" => function () {
                    return "ID";
                }
            ]
        ];
        $this->router->setRoutes($routes);

        $this->url->setPath("/");
        $this->assertEquals("/", $this->router->run());

        $this->url->setPath("/home/id333");
        $this->assertEquals("ID", $this->router->run());
    }

    public function testRegexp()
    {
        $route = [
            "methods" => [Route::METHOD_GET],
            "patterns" => [
                "part" => RoutePattern::PART_PATH,
                "type" => RoutePattern::TYPE_REGEXP,
                "value" => "/article/id(?P<ID>\w+)",
            ],
            "action" => function () {
                return "OK";
            }
        ];
        $this->router->setRoutes([$route]);
        $this->url->setPath("/article/id0ztgs");
        $this->assertEquals("OK", $this->router->run());
    }

    public function testSimpleRegexp()
    {
        $route = [
            "methods" => [Route::METHOD_GET],
            "patterns" => [
                "part" => RoutePattern::PART_PATH,
                "type" => RoutePattern::TYPE_REGEXP,
                "value" => "/test/{:name}/data/{:id}",
            ],
            "action" => function () {
                return "OK";
            }
        ];
        $this->router->setRoutes([$route]);
        $this->url->setPath("/test/ddd/data/aaaa");
        $this->assertEquals("OK", $this->router->run());
    }

    public function testArgsAction()
    {
        $route = [
            "methods" => [Route::METHOD_GET],
            "patterns" => [
                "part" => RoutePattern::PART_PATH,
                "type" => RoutePattern::TYPE_REGEXP,
                "value" => "/test/{:name}/data/{:id}",
            ],
            "action" => function ($arg, $params) {
                return $arg . $params["name"] . $params ["id"];
            },
            "args" => ["arg1"=>"test"],
        ];
        $this->router->setRoutes([$route]);
        $this->url->setPath("/test/isname/data/isid");
        $this->assertEquals("testisnameisid", $this->router->run());
    }

    public function testShortRoute()
    {
        $route = ["/test", function(){return "OK";}];
        $this->router->setRoutes([$route]);
        $this->url->setPath("/test");
        $this->assertEquals("OK", $this->router->run());
    }
}
