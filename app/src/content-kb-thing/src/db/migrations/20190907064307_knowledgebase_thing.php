<?php

use Phinx\Migration\AbstractMigration;
use Akademiano\Content\Knowledgebase\Thing\Model\ThingWorker;
use Akademiano\Content\Knowledgebase\Thing\Model\ThingImagesWorker;
use Akademiano\Content\Knowledgebase\Thing\Model\ThingThingRelationsWorker;
use Akademiano\Content\Articles\Model\ArticlesWorker;
use Akademiano\Content\Articles\Model\ArticleImagesWorker;
use Akademiano\EntityOperator\Worker\RelationsWorker;
use Akademiano\UserEO\Model\UsersWorker;
use Akademiano\EntityOperator\Command\GetTableIdCommand;

class KnowledgebaseThing extends AbstractMigration
{

    public function createThing(): void
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  PRIMARY KEY (id),
  FOREIGN KEY (owner) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (%s)
SQL;
        $sql = sprintf($sql,
            ThingWorker::TABLE_NAME,
            UsersWorker::TABLE_NAME,
            ArticlesWorker::TABLE_NAME
        );
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(ThingWorker::WORKER_ID))
        );
        $this->execute($sql);
    }

    public function createThingImage(): void
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  PRIMARY KEY (id),
  FOREIGN KEY (owner) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (entity) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (%s);
SQL;
        $sql = sprintf($sql,
            ThingImagesWorker::TABLE_NAME,
            UsersWorker::TABLE_NAME,
            ThingWorker::TABLE_NAME,
            ArticleImagesWorker::TABLE_NAME
        );
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(ThingImagesWorker::WORKER_ID))
        );
        $this->execute($sql);
    }

    public function createThingThingRelation(): void
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  first bigint NOT NULL,
  second bigint NOT NULL,
  PRIMARY KEY (id),
   FOREIGN KEY (first) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
   FOREIGN KEY (second) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
   FOREIGN KEY (owner) REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT  
)
INHERITS (%s);
SQL;
        $sql = sprintf($sql,
            ThingThingRelationsWorker::TABLE_NAME,
            ThingWorker::TABLE_NAME,
            ThingWorker::TABLE_NAME,
            UsersWorker::TABLE_NAME,
            RelationsWorker::TABLE_NAME
        );
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(ThingThingRelationsWorker::WORKER_ID))
        );
        $this->execute($sql);
    }


    public function up()
    {
        $this->createThing();
        $this->createThingImage();
        $this->createThingThingRelation();
    }
}
