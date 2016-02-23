<?php

use Phinx\Migration\AbstractMigration;

class AttachTypeExplodeToTwoFields extends AbstractMigration
{

    public function up()
    {
        if (!defined("ROOT_DIR")) {
            define('ROOT_DIR', realpath(__DIR__ . '/..'));
        }

        $files = $this->table("files");
        $files->addColumn("sub_type", "text", ['null' => true]);
        $files->update();

        $sql = "select * from files where TYPE  is NULL";
        $data = $this->query($sql);
        foreach ($data as $row) {
            $path = (mb_substr($row["path"], 0, 1) !== "/") ? ROOT_DIR . DIRECTORY_SEPARATOR . $row["path"] : $row["path"];
            $type = \DeltaUtils\FileSystem::getFileType($path);
            $this->execute("update files set type='{$type}' where id=" . $row["id"]);
        }

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
