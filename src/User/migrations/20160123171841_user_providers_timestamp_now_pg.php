<?php

use Phinx\Migration\AbstractMigration;

class UserProvidersTimestampNowPg extends AbstractMigration
{

    public function up()
    {
        $this->table('users_providers')
            ->changeColumn('created', 'timestamp', ['timezone' => false, "default" => "CURRENT_TIMESTAMP"])
            ->changeColumn('changed', 'timestamp', ['timezone' => false, "default" => "CURRENT_TIMESTAMP"])
            ->update();
    }

    public function down()
    {
        $this->table('users_providers')
            ->changeColumn('created', 'timestamp', ['timezone' => false])
            ->changeColumn('changed', 'timestamp', ['timezone' => false])
            ->update();
    }
}
