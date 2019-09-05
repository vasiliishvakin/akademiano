<?php


class MenuLoaderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\Menu\Model\MenuManager */
    protected $menuManager;

    protected $menuRaw;

    protected $routes;

    protected function _before()
    {
        $this->menuManager = new \Akademiano\Menu\Model\MenuManager();

        $this->menuRaw = new Akademiano\Config\Config([
            "main" => [
                ["text" => "Главная", "route" => "root", "order" => -10],
                ["text" => "example", "link" => "http://example.com"],
            ],
        ]);
    }

    protected function _after()
    {
        \Mockery::close();
        unset($this->pimple);
        unset($this->menuManager);
    }

    public function testAll()
    {
        $router = \Mockery::mock(\Akademiano\Router\Router::class);

        $route = \Mockery::mock(\Akademiano\Router\Route::class);

        $environment = \Mockery::mock(\Akademiano\HttpWarp\Environment::class);

        $url =  \Mockery::mock(\Akademiano\HttpWarp\Url::class);
        $url->shouldReceive("__toString()")->andReturn("/");


        $router->shouldReceive("getRoute")->andReturn($route);
        $router->shouldReceive("getCurrentRoute")->andReturn($route);
        $router->shouldReceive("getCurrentUrl")->andReturn($url);

        $route->shouldReceive("getUrl")->andReturn($url);
        $route->shouldReceive("getId")->andReturn("root");

        $environment->shouldReceive("getPort")->andReturn(80);
        $environment->shouldReceive("getSrvServerName")->andReturn("example-example.com");


        $this->menuManager->loadMenu($this->menuRaw);
        $this->menuManager->setRouter($router);
        $this->menuManager->setEnvironment($environment);
        $menu = $this->menuManager->getMenu("main");

        $this->tester->assertInstanceOf(\Akademiano\Menu\Model\Menu::class, $menu);

        $items = $menu->getItems();
        $this->tester->assertInstanceOf(\Akademiano\Utils\Object\Collection::class, $items);
        $this->tester->assertCount(2, $items);


    }
}