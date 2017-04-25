<?php


class ApplicationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\Core\Application */
    protected $application;

    /** @var  \Mockery\MockInterface|\Akademiano\HttpWarp\Environment */
    protected $environment;

    protected function _before()
    {
        $this->application = new \Akademiano\Core\Application();
        $loader = include ROOT_DIR . "/vendor/autoload.php";
        $this->application->setLoader($loader);
        $this->environment = \Mockery::mock(\Akademiano\HttpWarp\Environment::class);
        $this->application->addToDiContainer("environment", function () {
            return $this->environment;
        });
    }

    protected function _after()
    {
        \Mockery::close();
        if (isset($this->application)) {
            unset($this->application);
        }

        if (isset($this->environment)) {
            unset($this->environment);
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

        $this->environment->shouldReceive("getServerName")
            ->andReturn("_testsite_test");


        $request = \Mockery::mock(\Akademiano\HttpWarp\Request::class);

        $request->shouldReceive("getMethod")
            ->andReturn("GET");
        $request->shouldReceive("getUrl")
            ->andReturn($url);
        $request->shouldReceive("getEnvironment")
            ->andReturn($this->environment);

        $this->application->setRequest($request);
        $this->application->initRoutes($routes);
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

        $this->environment->shouldReceive("getServerName")
            ->andReturn("_testsite_test");


        $url = new \Akademiano\HttpWarp\Url("http://example.com/normal");

        $environment = \Mockery::mock(\Akademiano\HttpWarp\Environment::class);


        $request = \Mockery::mock(\Akademiano\HttpWarp\Request::class);


        $request->shouldReceive("getMethod")
            ->andReturn("GET");
        $request->shouldReceive("getUrl")
            ->andReturn($url);
        $request->shouldReceive("getEnvironment")
            ->andReturn($this->environment);

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

        $this->application->initRoutes($routes);

        $this->application->run();
    }


}