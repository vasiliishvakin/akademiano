<?php

namespace Akademiano\Core\Controller;


interface AdminControllerInterface extends ControllerInterface
{
    public function listAction();
    public function formAction(array $params = []);
    public function rmAction(array $params = []);
}
