<?php

use Akademiano\EntityOperator\Command\GetTableIdCommand;
use Akademiano\UserEO\Model\GroupsWorker;
use Phinx\Migration\AbstractMigration;
use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class EntityOperatorGroups extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE %s
(
  CONSTRAINT %s_pkey PRIMARY KEY (id)
)
INHERITS (%s);
SQL;
        $sql = sprintf($sql, GroupsWorker::TABLE_NAME, GroupsWorker::TABLE_NAME, NamedEntitiesWorker::TABLE_NAME);
        $this->execute($sql);

        $sql = sprintf(
            'CREATE SEQUENCE uuid_complex_short_tables_%d',
            (include dirname(__DIR__, 2) . '/vendor/akademiano/entity-operator/src/bootstrap.php')(new GetTableIdCommand(GroupsWorker::WORKER_ID))
        );
        $this->execute($sql);
    }
}
