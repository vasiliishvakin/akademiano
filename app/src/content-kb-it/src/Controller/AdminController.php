<?php


namespace Akademiano\Content\Knowledgebase\It\Controller;


use Akademiano\Content\Knowledgebase\It\AdminRoutesStore;
use Akademiano\Content\Knowledgebase\It\Model\ThingImagesWorker;

class AdminController extends \Akademiano\Content\Articles\Controller\AdminController
{
    const ENTITY_API_ID = IndexController::ENTITY_API_ID;
    const ENTITY_OPSR_STORE_CLASS = AdminRoutesStore::class;
    const FORM_FILES_FIELD = "files";


    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $data = $this->getRequest()->getParams();
        $entity = $this->getEntityApi()->save($data);

        $this->getEntityApi()->getFilesApi()->processHttpRequestFiles($this->getRequest(), $entity);

        $this->redirect($this->getEntityOpsRoutesStore()->getListRoute());
    }
}
