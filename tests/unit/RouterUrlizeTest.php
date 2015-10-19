<?php

use DeltaRouter\RoutePattern;
use DeltaRouter\Router;

class RouterUrlizeTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /**
     * @var \Router
     */
    protected $router;

    protected function _before()
    {
        $this->router = new Router();
    }

    protected function _after()
    {
        unset($this->router);
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

        $this->assertEquals("http://example.com/?action=test&p=3", (string)$router->getUrl("query", ["p"=>3, "action" => "test"]));
    }
}