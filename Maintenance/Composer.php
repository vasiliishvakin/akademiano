<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Maintenance;

use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Maintenance\EscClr;

class Composer
{
    public static function postInstallCmd(Event $event)
    {
        self::processPackages($event);
    }

    public static function postUpdateCmd(Event $event)
    {
        self::processPackages($event);
    }

    public static function processPackages(Event $event)
    {
        $composer = $event->getComposer();
        $installationManager = $composer->getInstallationManager();
        $vendorPath = $event->getComposer()->getConfig()->get('vendor-dir');
        $rootPath = dirname($vendorPath);
        $repositoryManager = $composer->getRepositoryManager();
        $repository = $repositoryManager->getLocalRepository();
        /** @var Package[] $packages */
        $packages = $repository->getPackages();
        foreach ($packages as $package) {
            $pathPackage = $installationManager->getInstallPath($package);
            $pathClasses = self::getPackagePatch($package);
            foreach ($pathClasses as $path) {
                $path = $pathPackage . "/" . $path;
                $className = basename($path);
                echo "process package $className \n";
                self::tryAddMigration($path, $rootPath);
            }
        }
        self::processModules($rootPath);
    }


    public static function getPackagePatch(PackageInterface $package)
    {
        $autoload = $package->getAutoload();
        $patchArr = [];
        foreach ($autoload as $type => $clItems) {
            foreach ($clItems as $class => $path) {
                if ($type === "psr-0") {
                    $patchArr[] = $path . $class;
                }
            }
        }
        array_unique($patchArr);
        return $patchArr;
    }

    public static function tryAddMigration($path, $rootPath)
    {
        $mgrPackagePath = $path . "/migrations";
        if (!file_exists($mgrPackagePath)) {
//            echo EscClr::fg("dark_gray", "not exist $mgrPackagePath") . "\n";
            return;
        }
        $migrationsDir = $rootPath . "/migrations";
        $migrations = array_diff(scandir($mgrPackagePath), array('..', '.'));
        foreach ($migrations as $file) {
            if (strpos($file, "_mysql.php") !== false) {
                continue;
            }
            $filePath = $mgrPackagePath . '/' . $file;
            $dist = $migrationsDir . "/" . $file;
            if (!file_exists($dist)) {
                copy($filePath, $dist);
                echo EscClr::fg("green", "install migration $file") . "\n";
            }
        }
    }

    public static function processModules($rootPath)
    {
        $modDir = $rootPath . "/modules";
        if (!file_exists($modDir)) {
            return;
        }
        $modules = array_diff(scandir($modDir), array('..', '.'));
        foreach($modules as $moduleDir) {
            $moduleName = basename($moduleDir);
            echo "process module $moduleName \n";
            self::tryAddMigration($modDir . "/" .$moduleDir, $rootPath);
        }
    }

}