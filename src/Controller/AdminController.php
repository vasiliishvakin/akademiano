<?php


namespace Akademiano\Content\Articles\Controller;


use Akademiano\Attach\Model\LinkedFilesWorker;
use Akademiano\Content\Articles\AdminRoutesStore;
use Akademiano\Content\Articles\Api\v1\ArticleTagsApi;

class AdminController extends IndexController
{
    const ENTITY_OPSR_STORE_CLASS = AdminRoutesStore::class;

    const RELATIONS = [
        'tags' => ArticleTagsApi::API_ID,
    ];

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $data = $this->getRequest()->getParams();
        $entity = $this->getEntityApi()->save($data);

        $this->getEntityApi()->getFilesApi()->processHttpRequestFiles($this->getRequest(), $entity);



        /*$files = $this->getRequest()->getFiles(static::FORM_FILES_FIELD);

        foreach ($files as $file) {
            $this->getEntityApi()->getFilesApi()->saveUploaded($file, [LinkedFilesWorker::LINKED_ENTITY_FIELD => $entity]);
        }*/
        $this->redirect($this->getEntityOpsRoutesStore()->getListRoute());
    }
}
