<?php

use Phinx\Migration\AbstractMigration;

class RelatedFiles extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE linked_files
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
  entity bigint,
  PRIMARY KEY (id)
  FOREIGN KEY (entity) REFERENCES entities (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (files);
SQL;
        $this->execute($sql);


        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Attach\Model\LinkedFilesWorker::TABLE_ID);
        $this->execute($sql);
    }
}
