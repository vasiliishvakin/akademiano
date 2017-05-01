<?php

namespace Sites\_Default\Controller;


use Akademiano\Core\ApplicationController;

class IndexController extends ApplicationController
{
    public function indexAction()
    {
        $dataStorage = $this->getDatStorage();
        $file = $dataStorage->getFileOrThrow("default-site-demo-data.example.txt");
        $title = file_get_contents($file);
        return [
            "title" => $title,
        ];
    }
}
