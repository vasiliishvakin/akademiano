<?php

use Akademiano\Router\RoutePattern;
use Akademiano\Router\Router;

class RouterUrlizeTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /**
     * @var Router
     */
    protected $router;

    /** @var  \Mockery\MockInterface */
    protected $environment;

    protected $request;

    protected function _before()
    {
        $environment = \Mockery::mock(\Akademiano\HttpWarp\Environment::class);
        $environment->shouldReceive("getPort")->andReturn(80);
        $environment->shouldReceive("getScheme")->andReturn('http');

        $request = \Mockery::mock(\Akademiano\HttpWarp\Request::class);
        $request->shouldReceive("getEnvironment")->andReturn($environment);

        $this->environment = $environment;

        $this->router = new Router($request);

    }

    protected function _after()
    {
        unset($this->router);
        unset($this->environment);
        unset($this->request);
    }

    // tests
    public function testDomainUrlize()
    {
        $router = $this->router;
        /** Router $router */
        $routes = [
            "domain_full" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_DOMAIN,
                        "type" => RoutePattern::TYPE_FULL,
                        "value" => "example.com",
                    ],
                    "action" => function () {}
                ],
            "domain_prefix" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_DOMAIN,
                        "type" => RoutePattern::TYPE_PREFIX,
                        "value" => "example.com",
                    ],
                    "action" => function () {
                    }
                ],
            "domain_regexp_lite" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_DOMAIN,
                        "type" => RoutePattern::TYPE_REGEXP,
                        "value" => "{:subname}.example.com",
                    ],
                    "action" => function () {
                    }
                ],
            "domain_regexp" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_DOMAIN,
                        "type" => RoutePattern::TYPE_REGEXP,
                        "value" => "(?P<subname>\\w+).example.com",
                    ],
                    "action" => function () {
                    }
                ],
        ];
        $router->setRoutes($routes);

        $url = $router->getUrl("domain_full");
        $url->__toString();

        $this->assertEquals("http://example.com", (string)$router->getUrl("domain_full"));
        $this->assertEquals("http://example.com.ru", (string)$router->getUrl("domain_prefix", ["ru"]));
        $this->assertEquals("http://test.example.com", (string)$router->getUrl("domain_regexp_lite", ["subname" => "test"]));
        $this->assertEquals("http://test.example.com", (string)$router->getUrl("domain_regexp", ["subname" => "test"]));
    }

    public function testPathUrlize()
    {
        $_SERVER["HTTP_HOST"] = "example.com";
        $router = $this->router;
        /** Router $router */
        $routes = [
            "path_full" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_PATH,
                        "type" => RoutePattern::TYPE_FULL,
                        "value" => "/example",
                    ],
                    "action" => function () {}
                ],
            "path_prefix" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_PATH,
                        "type" => RoutePattern::TYPE_PREFIX,
                        "value" => "/example",
                    ],
                    "action" => function () {
                    }
                ],
            "path_regexp_lite" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_PATH,
                        "type" => RoutePattern::TYPE_REGEXP,
                        "value" => "/example/{:subname}",
                    ],
                    "action" => function () {
                    }
                ],
            "path_regexp" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_PATH,
                        "type" => RoutePattern::TYPE_REGEXP,
                        "value" => "/example/(?P<subname>\\w+)",
                    ],
                    "action" => function () {
                    }
                ],
        ];
        $router->setRoutes($routes);

        $this->environment->shouldReceive("getServerName")->andReturn("example.com");

        $this->assertEquals("http://example.com/example", (string)$router->getUrl("path_full"));
        $this->assertEquals("http://example.com/example/test", (string)$router->getUrl("path_prefix", ["test"]));
        $this->assertEquals("http://example.com/example/test", (string)$router->getUrl("path_regexp_lite", ["subname" => "test"]));
        $this->assertEquals("http://example.com/example/test", (string)$router->getUrl("path_regexp", ["subname" => "test"]));
    }

    public function testQueryUrlize()
    {
        $_SERVER["HTTP_HOST"] = "example.com";
        $router = $this->router;
        /** Router $router */
        $routes = [
            "query" =>
                [
                    "patterns" => [
                        "part" => RoutePattern::PART_QUERY,
                        "value" => ["p"=>1],
                    ],
                    "action" => function () {}
                ],
        ];
        $router->setRoutes($routes);

        $this->environment->shouldReceive("getServerName")->andReturn("example.com");

        $this->assertEquals("http://example.com/?action=test&p=3", (string)$router->getUrl("query", ["p"=>3, "action" => "test"]));
    }
}