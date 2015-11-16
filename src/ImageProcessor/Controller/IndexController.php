<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace ImageProcessor\Controller;

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

    public function IndexAction(array $params = [])
    {
        $template = $params["template"];
        $fileDir = $params["directory"];
        $fileName = $params["file"];
        $filePath = $fileDir . "/" .$fileName;

        $imagesDir = $this->getConfig(["ImageProcessor", "directory"], "data/images");
        $imagesDir = ROOT_DIR . "/" . $imagesDir;
        $realPath = ROOT_DIR . $filePath;
        $path = FileSystem::inDir($imagesDir, $realPath);
        if (!$path) {
            throw new \Exception("file not in images dir");
        }
        $pubDir = ROOT_DIR . "/public";
        $pubPath = ROOT_DIR . "/public" . $fileDir;

        $realPubPath = FileSystem::getSubDir($pubDir,  $pubPath, false);
        if (!$realPubPath) {
            throw new \Exception("Path not allow $pubPath in $pubDir");
        }
        $pubPath = $realPubPath . "/" . $template . "/" . $fileName;

        $imp = $this->getImageProcessor();
        $imp->process($realPath, $pubPath, $template);

        //TODO use accel
        Header::mime($pubPath);
        echo file_get_contents($pubPath);
    }
}
