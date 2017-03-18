<?php


class ApplicationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\Core\Application */
    protected $application;

    protected function _before()
    {
        $this->application = new \Akademiano\Core\Application();
        $loader = include ROOT_DIR . "/vendor/autoload.php";
        $this->application->setLoader($loader);
    }

    protected function _after()
    {
        \Mockery::close();
        if (isset($this->application)) {
            unset($this->application);
        }
    }

    // tests
    public function testConfig()
    {
        $configLoader = \Mockery::mock(\Akademiano\Config\ConfigLoader::class);
        $this->application->setConfigLoader($configLoader);
        $config = [
            "modules" =>
                [
                    "entity",
                ],
        ];

        $configObj = new \Akademiano\Config\Config($config);
        $configLoader->shouldReceive("getConfig")
            ->andReturn($configObj);

        $this->assertInstanceOf(
            \Akademiano\Config\ConfigLoader::class,
            $this->application->getConfigLoader()
        );

        $this->assertEquals($configObj, $this->application->getConfig());
    }

    public function testRunSimple()
    {
        $testObj = \Mockery::mock(stdClass::class);
        $testObj->shouldReceive("indexAction")->once()->andReturn(true);

        $routes = [
            "root" => ["/", function () use ($testObj) {
                echo "ok";
                $testObj->indexAction();
                return "ok";
            }],
            "module" => [
                "/module",
                [
                    "module" => "Akademiano\\Core\\Tests\\Data",
                    "controller" => "example",
                    "action" => "index",
                ]
            ],
        ];


        $url = new \Akademiano\HttpWarp\Url("http://example.com/");


        $request = \Mockery::mock(\Akademiano\HttpWarp\Request::class);
        $request->shouldReceive("getMethod")
            ->andReturn("GET");
        $request->shouldReceive("getUrl")
            ->andReturn($url);

        $this->application->setRequest($request);
        $this->application->setRoutes($routes);
        $this->application->run();
    }

    public function testRunNormal()
    {
        $routes = [
            "normal" => [
                "/normal",
                [
                    [
                        "module" => "Akademiano\\Core\\Tests\\Data",
                        "controller" => "example",
                    ],
                    "action" => "index",
                ]
            ],
        ];


        $url = new \Akademiano\HttpWarp\Url("http://example.com/normal");


        $request = \Mockery::mock(\Akademiano\HttpWarp\Request::class);
        $request->shouldReceive("getMethod")
            ->andReturn("GET");
        $request->shouldReceive("getUrl")
            ->andReturn($url);

        $response = Mockery::mock(\Akademiano\HttpWarp\Response::class);
        $response->shouldReceive("setDefaults");
        $response->shouldReceive("setBody")->once();
        $response->shouldReceive("sendReplay")->once();

        $view = Mockery::mock(\Akademiano\SimplaView\View::class);
        $view->shouldReceive("assignArray")->twice()->andReturnNull();
        $view->shouldReceive("exist")->andReturn(false);
        $view->shouldReceive("setTemplate");
        $view->shouldReceive("render");


        $this->application->setRequest($request);
        $this->application->setResponse($response);
        $this->application->setView($view);

        $this->application->setRoutes($routes);

        $this->application->run();
    }


}