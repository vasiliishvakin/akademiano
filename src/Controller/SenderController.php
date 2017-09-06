<?php


namespace Akademiano\Messages\Controller;


use Akademiano\Core\ApplicationController;
use Akademiano\Messages\Api\v1\SendEmailsApi;

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
