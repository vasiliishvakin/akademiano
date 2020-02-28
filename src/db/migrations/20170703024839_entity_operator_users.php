<?php

use Akademiano\EntityOperator\Command\GetTableIdCommand;
use Akademiano\UserEO\Model\UsersWorker;
use Phinx\Migration\AbstractMigration;
use Akademiano\UserEO\Model\GroupsWorker;
use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class EntityOperatorUsers extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  email text,
  password text,
  phone text,
  "group" bigint,
  PRIMARY KEY (id),
  CONSTRAINT email UNIQUE (email),
  FOREIGN KEY ("group") REFERENCES %s (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (%s);
SQL;
        $sql = sprintf($sql,
            UsersWorker::TABLE_NAME,
            GroupsWorker::TABLE_NAME,
            NamedEntitiesWorker::TABLE_NAME
        );
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(UsersWorker::WORKER_ID))
        );
        $this->execute($sql);
    }
}
