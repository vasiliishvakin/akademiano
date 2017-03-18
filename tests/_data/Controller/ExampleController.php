<?php


namespace Akademiano\Core\Tests\Data\Controller;



use Akademiano\Core\Controller\AbstractController;

class ExampleController extends AbstractController
{

    public function indexAction()
    {
        return [
            "result" => true,
        ];
    }

}
