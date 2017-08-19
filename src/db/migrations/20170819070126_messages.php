<?php

use Phinx\Migration\AbstractMigration;

class Messages extends AbstractMigration
{

    public function up()
    {
        $sql = <<<SQL
        
CREATE TABLE messages
(
-- Унаследована from table content:  id bigint NOT NULL,
-- Унаследована from table content:  created timestamp without time zone,
-- Унаследована from table content:  changed timestamp without time zone,
-- Унаследована from table content:  active boolean DEFAULT true,
-- Унаследована from table content:  owner bigint,
-- Унаследована from table content:  title text,
-- Унаследована from table content:  description text,
-- Унаследована from table content:  content text,
  from bigint,
  to bigint,
  type smallint,
  params jsonb,
  status smallint,
  PRIMARY KEY (id),
  FOREIGN KEY (from) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (to) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
)
INHERITS (content)
SQL;
        $this->execute($sql);

        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%s', \Akademiano\Messages\Model\MessagesWorker::TABLE_ID);
        $this->execute($sql);
    }
}
