<?php

use Phinx\Migration\AbstractMigration;


class UuidComplexShortTables extends AbstractMigration
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

        $sql = <<<sql
CREATE OR REPLACE FUNCTION public.uuid_short_complex_tables(IN table_id integer DEFAULT 1) RETURNS bigint AS
$$
DECLARE
    our_epoch bigint := $epoch;
    seq_id bigint;
    now_millis bigint;
    shard_id integer := $shard;
    result bigint;
BEGIN
    if (table_id < 1) or (table_id > 512) then
        return null;
    end if;

    SELECT nextval('uuid_complex_short_tables_' || table_id) % 1024 INTO seq_id;

    SELECT FLOOR(EXTRACT(EPOCH FROM clock_timestamp()) * 1000) INTO now_millis;
    result := (now_millis - our_epoch) << 23;
    result := result | (shard_id << 19);
    result := result | (table_id << 10);
    result := result | (seq_id);
    RETURN result;
END;
$$
LANGUAGE plpgsql VOLATILE LEAKPROOF;
sql;
        $this->execute($sql);

    }
}
