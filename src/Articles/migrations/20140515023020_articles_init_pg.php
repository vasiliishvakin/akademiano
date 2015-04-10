<?php

use Phinx\Migration\AbstractMigration;

class ArticlesInitPg extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE articles
(
  id serial NOT NULL,
  title text,
  description text,
  text text,
  created date,
  changed timestamp with time zone,
  CONSTRAINT articles_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

CREATE TABLE article_categories
(
  id serial NOT NULL,
  name text,
  CONSTRAINT article_categories_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

CREATE TABLE article_categories_matrix
(
  id serial NOT NULL,
  article integer,
  category integer,
  CONSTRAINT article_categories_matrix_pkey PRIMARY KEY (id),
  CONSTRAINT article_categories_matrix_article_fkey FOREIGN KEY (article)
      REFERENCES articles (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT article_categories_matrix_category_fkey FOREIGN KEY (category)
      REFERENCES article_categories (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);

SQL;
        $this->execute($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}