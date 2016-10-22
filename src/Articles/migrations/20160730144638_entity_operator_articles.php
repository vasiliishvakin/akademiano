<?php

use Phinx\Migration\AbstractMigration;

class EntityOperatorArticles extends AbstractMigration
{
    public function up()
    {
        //id table 14
        $table = $this->table("articles");
        $table->rename("articles_old");
        $table->save();

        $sql = <<<SQL
CREATE TABLE articles
(
-- Унаследована from table named:  id bigint NOT NULL,
-- Унаследована from table named:  created timestamp without time zone,
-- Унаследована from table named:  changed timestamp without time zone,
-- Унаследована from table named:  active boolean DEFAULT true,
-- Унаследована from table named:  title text,
-- Унаследована from table named:  description text,
  CONSTRAINT articles_em_pkey PRIMARY KEY (id)
)
INHERITS (content);
SQL;
        $this->execute($sql);

        $table = $this->table("articles");
        $table->addColumn("old_id", "integer", ['null' => true]);
        $table->save();

        $tableId = 14;

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_{$tableId}";
        $this->execute($sql);

        $sql = "insert into articles (id, created, changed, active, title, description, content, old_id) select  uuid_short_complex_tables({$tableId}), created, changed, true, title, description, \"text\", id from articles_old";

        $this->execute($sql);
    }
}
