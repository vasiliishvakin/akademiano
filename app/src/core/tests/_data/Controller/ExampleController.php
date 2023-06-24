<?php


namespace Akademiano\Core\Tests\Data\Controller;



use Akademiano\Core\Controller\AkademianoController;

class ExampleController extends AkademianoController
{

    public function indexAction()
    {
        return [
            "result" => true,
        ];
    }

}
