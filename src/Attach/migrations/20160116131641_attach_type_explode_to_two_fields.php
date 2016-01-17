<?php

use Phinx\Migration\AbstractMigration;

class AttachTypeExplodeToTwoFields extends AbstractMigration
{

    public function up()
    {
        $files = $this->table("files");
        $files->addColumn("sub_type", "text", ['null' => true]);
        $files->update();

        $sql = "update files set sub_type=split_part(type, '/', 2), type=split_part(type, '/', 1)";
        $this->execute($sql);

    }

    public function down()
    {
        $sql = "update files set type=type || '/' || sub_type";
        $this->execute($sql);

        $files = $this->table("files");
        $files->removeColumn("sub_type");
        $files->update();
    }
}
