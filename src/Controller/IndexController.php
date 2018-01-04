<?php

namespace Akademiano\Content\Articles\Controller;


use Akademiano\Attach\Model\LinkedFilesWorker;
use Akademiano\Content\Articles\Api\v1\ArticlesApi;
use Akademiano\Content\Articles\RoutesStore;
use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;

/**
 * @method ArticlesApi getEntityApi()
 */
class IndexController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = RoutesStore::class;
    const ENTITY_API_ID = ArticlesApi::API_ID;
    const FORM_FILES_FIELD = "files";

    public function getListCriteria()
    {
        return [];
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $data = $this->getRequest()->getParams();
        $entity = $this->getEntityApi()->save($data);

        $files = $this->getRequest()->getFiles(static::FORM_FILES_FIELD);

        foreach ($files as $file) {
            $this->getEntityApi()->getFilesApi()->saveUploaded($file, [LinkedFilesWorker::LINKED_ENTITY_FIELD => $entity]);
        }
        $this->redirect($this->getEntityOpsRoutesStore()->getListRoute());
    }
}
