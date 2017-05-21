<?php


class SitesManagerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\Sites\SitesManager */
    protected $sitesManager;

    protected function _before()
    {
        $classLoader = include __DIR__ . "/../../vendor/autoload.php";
        $environment = Mockery::mock(\Akademiano\HttpWarp\Environment::class);
        $this->sitesManager = new \Akademiano\Sites\SitesManager($classLoader, $environment);
        $this->sitesManager->setRootDir(__DIR__ . "/../../");
    }

    protected function _after()
    {
        unset($this->sitesManager);
        Mockery::close();
    }

    public function testMain()
    {
        $this->tester->assertNull($this->sitesManager->getSite("not_exist_site"));
        $this->tester->expectException(\Exception::class, function () {
            $this->sitesManager->getSite("/tmp");
        });


        $siteName = "_testsite_test";
        $site = $this->sitesManager->getSite($siteName);

        $this->tester->assertInstanceOf(\Akademiano\Sites\SiteInterface::class, $site);
        $themesDir = $site->getThemesDir();
        $this->tester->assertInstanceOf(\Akademiano\Sites\Site\ThemesDir::class, $themesDir);

        $this->tester->assertNull($themesDir->getTheme("not-exist"));
        $this->tester->assertInstanceOf(\Akademiano\Sites\Site\Theme::class, $themesDir->getTheme("test-theme"));


        $tmpDir = sys_get_temp_dir();
        $tempSubDir = tempnam($tmpDir, '');
        $tmpPartSubDir = basename($tempSubDir);
        unlink($tempSubDir);
        mkdir($tempSubDir);

        if (!is_dir($pubSitesDir = $tempSubDir . DIRECTORY_SEPARATOR . \Akademiano\Sites\Site\PublicStorage::GLOBAL_DIR)) {
            mkdir($pubSitesDir, 0777);
        }
        $tempSubDir = realpath($tempSubDir);


        $rootDir = $site->getRootDir();
        $site->setRootDir($tempSubDir);
        $publicGlobalPath = $site->getPublicGlobalPath();
        $this->tester->assertEquals($tempSubDir . DIRECTORY_SEPARATOR . \Akademiano\Sites\Site\PublicStorage::GLOBAL_DIR, $site->getPublicDir());
        $site->setRootDir($rootDir);

        $this->tester->assertEquals(
            $tempSubDir . DIRECTORY_SEPARATOR . \Akademiano\Sites\Site\PublicStorage::GLOBAL_DIR . DIRECTORY_SEPARATOR . $siteName,
            $publicGlobalPath
        );

        $this->tester->assertEquals("/" . \Akademiano\Sites\Site\PublicStorage::GLOBAL_DIR . "/" . $siteName, $site->getPublicWebPath());

        $publicStore = $site->getPublicStorage();
        $this->tester->assertInstanceOf(\Akademiano\Sites\Site\PublicStorage::class, $publicStore);

        $this->tester->assertNull($publicStore->getFile("not-exist-file"));
        $this->tester->expectException(
            \Akademiano\HttpWarp\Exception\AccessDeniedException::class,
            function () use ($publicStore) {
                return $publicStore->getFile("../../../../../composer.json");
            }
        );

        $fileName = "test-file.txt";
        $testFile = $publicStore->getFile($fileName);
        $this->tester->assertInstanceOf(\Akademiano\Sites\Site\File::class, $testFile);
        $this->tester->assertEquals("/" . \Akademiano\Sites\Site\PublicStorage::GLOBAL_DIR . "/" . $siteName . "/" . $fileName, $testFile->getWebPath());
        $this->tester->assertEquals($pubSitesDir . DIRECTORY_SEPARATOR . $siteName . DIRECTORY_SEPARATOR . $fileName, $testFile->getPath());
        $this->tester->assertEquals("test-file", $testFile->getContent());
    }
}
