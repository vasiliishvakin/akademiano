<?php

use Phinx\Migration\AbstractMigration;

class HeraldMessages extends AbstractMigration
{

    public function up()
    {
        $sql = <<<SQL
        
CREATE TABLE herald_messages
(
-- Унаследована from table content:  id bigint NOT NULL,
-- Унаследована from table content:  created timestamp without time zone,
-- Унаследована from table content:  changed timestamp without time zone,
-- Унаследована from table content:  active boolean DEFAULT true,
-- Унаследована from table content:  owner bigint,
-- Унаследована from table content:  title text,
-- Унаследована from table content:  description text,
-- Унаследована from table content:  content text,
  "from" text,
  "to" text,
  "transport" smallint,
  "data" jsonb,
  "params" jsonb,
  "status" smallint,  
  PRIMARY KEY (id)
)
INHERITS (content)
SQL;
        $this->execute($sql);

        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%s', \Akademiano\HeraldMessages\Model\MessagesWorker::TABLE_ID);
        $this->execute($sql);
    }
}
