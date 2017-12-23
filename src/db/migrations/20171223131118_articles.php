<?php

use Phinx\Migration\AbstractMigration;

class Articles extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
CREATE TABLE articles
(
  CONSTRAINT tasks_pkey PRIMARY KEY (id),
  CONSTRAINT tasks_owner FOREIGN KEY (owner)
      REFERENCES public.users (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (content)
SQL;
        $this->execute($sql);

        $tableId = \Akademiano\Content\Articles\Model\ArticleWorker::TABLE_ID;

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_{$tableId}";
        $this->execute($sql);
    }
}
