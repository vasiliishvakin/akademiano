<?php

use Phinx\Migration\AbstractMigration;

class AttachFiles extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE files
(
-- Унаследована from table named:  id bigint NOT NULL,
-- Унаследована from table named:  created timestamp without time zone,
-- Унаследована from table named:  changed timestamp without time zone,
-- Унаследована from table named:  active boolean DEFAULT true,
-- Унаследована from table named:  title text,
-- Унаследована from table named:  description text,
  type text, 
  sub_type text, 
  path text, 
  position text, 
  size integer, 
  mime_type text,
  PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);


        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Attach\Model\FilesWorker::TABLE_ID);
        $this->execute($sql);
    }
}
