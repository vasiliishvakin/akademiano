<?php

namespace Image\Controller;

use DeltaCore\AbstractController;
use DeltaUtils\FileSystem;
use HttpWarp\Header;
use ImageProcessor\Model\Processor;

class IndexController extends AbstractController
{

    /**
     * @return Processor
     */
    public function getImageProcessor()
    {
        $app = $this->getApplication();

        return $app["imageProcessor"];
    }

    public function indexAction(array $params = [])
    {
        $filters = [
            'template' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => ["regexp" => "~\\w+~"],
            ],
            'parentDir' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => ["regexp" => "~[a-z0-9/]+~"],
            ],
            'subDir' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => ["regexp" => "~[a-z0-9/]+~"],
            ],
            'file' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => ["regexp" => "~\\w+\\.\\w+~"],
            ],
        ];
        $params = filter_var_array($params, $filters);

        $template = $params["template"];
        $parentDir = $params["parentDir"];
        $subDir = $params["subDir"];
        $fileDir = $parentDir . "/" . $subDir;
        $fileName = $params["file"];
        $filePath = $fileDir . "/" . $fileName;

        $imagesDir = $this->getConfig(["Image", "directory"], "data/images");
        $imagesDir = ROOT_DIR . "/" . $imagesDir;
        $realPath = ROOT_DIR . "/" . $filePath;
        $path = FileSystem::inDir($imagesDir, $realPath);
        if (!$path) {
            throw new \Exception("file not in images dir");
        }
        $pubDir = ROOT_DIR . "/public";
        $pubPath = $pubDir . "/" . $parentDir . "/" . $template . "/" .  $subDir;

        $realPubPath = FileSystem::getSubDir($pubDir, $pubPath, false);
        if (!$realPubPath) {
            throw new \Exception("Path not allow $pubPath in $pubDir");
        }
        $pubPath = "/" . $realPubPath . "/" . $fileName;

        $outFullPath = ROOT_DIR . "/public" . $pubPath;

        $imp = $this->getImageProcessor();
        $outFile = $imp->process($realPath, $outFullPath, $template);

        Header::accel($pubPath, $outFile);
    }
}
