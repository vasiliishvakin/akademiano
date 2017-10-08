<?php


namespace Akademiano\HeraldMessages\Controller;


use Akademiano\Core\ApplicationController;
use Akademiano\HeraldMessages\Api\v1\SendEmailsApi;

class SenderController extends ApplicationController
{
    /** @var  SendEmailsApi */
    protected $sendEmailApi;

    /**
     * @return SendEmailsApi
     */
    public function getSendEmailsApi(): SendEmailsApi
    {
        return $this->getDiContainer()[SendEmailsApi::API_ID];
    }

    public function sendEmailsAction()
    {
        $items = $this->getSendEmailsApi()->processQueue();
        return ["items" => $items];
    }
}
