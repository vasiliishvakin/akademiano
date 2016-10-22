<?php

use Phinx\Migration\AbstractMigration;
use DeltaCore\Application;
use DeltaPhp\Operator\Command\GenerateIdCommand;
use Attach\Model\ImageFileEntity;
use Articles\Model\ArticleImageRelation;

class ArticleImagesToRelation extends AbstractMigration
{
    public function up()
    {
        /** @var \DeltaCore\Application $app */
        $app = include __DIR__ . "/../App/bootstrap.php";
        $app->init();

        /** @var \Attach\Model\FileManager $fm */
        $fm = $app["fileManager"];

        $section = (integer) $fm->getSection(\Articles\Model\Article::class);

        $sql = "select id old_id, object, \"name\", description, type, sub_type, path, \"order\" info, main, created from files_old where type='image' and section={$section}";
        $files  = $this->fetchAll($sql);

        /** @var \DeltaPhp\Operator\EntityOperator $operator */
        $operator = $app["Operator"];

        $getUuidFunction = function($class) use ($operator){
            $idGenerateCommand = new GenerateIdCommand($class);
            $id = $operator->execute($idGenerateCommand);
            return $id;
        };

        foreach ($files as $fileData) {
            $article = $operator->find(\Articles\Model\Article::class, ["old_id" => $fileData["object"]])->firstOrFalse();
            if (!$article){
                continue;
            }
            /** @var \Attach\Model\FileEntity $fileObject */
            $fileObject = $operator->create(ImageFileEntity::class);
            $fileData["id"] = $getUuidFunction(ImageFileEntity::class);
            $operator->load($fileObject, $fileData);
            usleep(mt_rand(0, 1000000));
            $operator->save($fileObject);

            $relation = $operator->create(ArticleImageRelation::class);
            $relation->setId($getUuidFunction(ArticleImageRelation::class));
            $relation->setFirst($article);
            $relation->setSecond($fileObject);
            usleep(mt_rand(0, 1000000));
            $operator->save($relation);
        }
    }
}
