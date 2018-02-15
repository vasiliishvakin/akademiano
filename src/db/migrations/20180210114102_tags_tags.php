<?php

use Phinx\Migration\AbstractMigration;

class TagsTags extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE tags_tags
(
  PRIMARY KEY (id),
  FOREIGN KEY (owner)
      REFERENCES public.users (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (named);
SQL;
        $this->execute($sql);

        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Tags\Model\TagsWorker::TABLE_ID);
        $this->execute($sql);
    }
}
