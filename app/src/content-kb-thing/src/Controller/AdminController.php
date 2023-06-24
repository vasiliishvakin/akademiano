<?php


namespace Akademiano\Content\Knowledgebase\Thing\Controller;


use Akademiano\Content\Knowledgebase\Thing\AdminRoutesStore;
use Akademiano\Content\Knowledgebase\Thing\Model\ThingImagesWorker;

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
