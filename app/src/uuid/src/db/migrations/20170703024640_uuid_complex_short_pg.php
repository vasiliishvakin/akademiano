<?php

use Phinx\Migration\AbstractMigration;


class UuidComplexShortPg extends AbstractMigration
{
    const OUR_EPOCH_DEFAULT = 1498995192141;

    public function up()
    {
        /** @var \Akademiano\Core\Application $app */
        $app = include __DIR__ . "/../../vendor/akademiano/core/src/bootstrap.php";
        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $app->getDiContainer()["baseConfigLoader"];
        $configLoader->addConfigDir(__DIR__ . '/../../src/config', PHP_INT_MAX);
        $app->init();

        $epoch = $app->getConfig()->get(["UUID", "complexShort", "epoch"], self::OUR_EPOCH_DEFAULT);
        $shard = $app->getConfig(["UUID", "complexShort", "shard"], 1);

        $sql = "create sequence uuid_complex_short_part;";

        $this->execute($sql);

        $sql = <<<sql
CREATE OR REPLACE FUNCTION uuid_short_complex(OUT result bigint) AS $$
DECLARE
    our_epoch bigint := $epoch;
    seq_id bigint;
    now_millis bigint;
    shard_id int := $shard;
BEGIN
    SELECT nextval('uuid_complex_short_part') % 1024 INTO seq_id;

SELECT FLOOR(EXTRACT(EPOCH FROM clock_timestamp()) * 1000) INTO now_millis;
    result := (now_millis - our_epoch) << 23;
    result := result | (shard_id << 10);
    result := result | (seq_id);
END;
$$ LANGUAGE PLPGSQL;
sql;
        $this->execute($sql);

    }

    public function down()
    {
        $sql = "DROP FUNCTION IF EXISTS uuid_short_complex(OUT result bigint);";
        $this->execute($sql);

        $sql = "DROP SEQUENCE IF EXISTS uuid_complex_short_part;";
        $this->execute($sql);
    }
}
