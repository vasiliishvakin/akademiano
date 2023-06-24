<?php

use Phinx\Migration\AbstractMigration;

class LinkedImages extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE linked_images
(
  "order" integer NOT NULL DEFAULT 0,
  main boolean DEFAULT false,
  PRIMARY KEY (id),
  FOREIGN KEY (owner) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  FOREIGN KEY (entity) REFERENCES entities (id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
INHERITS (linked_files);
SQL;
        $this->execute($sql);


        $sql = sprintf('CREATE SEQUENCE uuid_complex_short_tables_%d', \Akademiano\Content\Files\Images\Model\LinkedImagesWorker::TABLE_ID);
        $this->execute($sql);
    }
}
