<?php


namespace Akademiano\Content\Knowledgebase\Thing\Controller;


use Akademiano\Content\Articles\AdminRoutesStore;
use Akademiano\Content\Knowledgebase\Thing\Model\ThingImagesWorker;

class AdminController extends IndexController
{
    const ENTITY_OPSR_STORE_CLASS = AdminRoutesStore::class;


    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $data = $this->getRequest()->getParams();
        $entity = $this->getEntityApi()->save($data);

        $this->getEntityApi()->getFilesApi()->processHttpRequestFiles($this->getRequest(), $entity);


        $files = $this->getRequest()->getFiles(static::FORM_FILES_FIELD);

        foreach ($files as $file) {
            $this->getEntityApi()->getFilesApi()->saveUploaded($file, [ThingImagesWorker::LINKED_ENTITY_FIELD => $entity]);
        }
        $this->redirect($this->getEntityOpsRoutesStore()->getListRoute());
    }
}
