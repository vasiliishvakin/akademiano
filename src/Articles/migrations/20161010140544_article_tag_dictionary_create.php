<?php

use Phinx\Migration\AbstractMigration;

class ArticleTagDictionaryCreate extends AbstractMigration
{
    public function up()
    {
        /** @var \DeltaCore\Application $app */
        $app = include __DIR__ . "/../App/bootstrap.php";
        $app->init();

        /** @var \DeltaPhp\Operator\EntityOperator $operator */
        $operator = $app["Operator"];

        $entity = $operator->find(\DeltaPhp\TagsDictionary\Entity\Dictionary::class, ["title" => "Article Tags"])->firstOrFalse();
        if (!$entity) {
            /** @var \DeltaPhp\TagsDictionary\Entity\Dictionary $entity */
            $entity = $operator->create(\DeltaPhp\TagsDictionary\Entity\Dictionary::class);
            $entity->setTitle("Article Tags");
            $operator->save($entity);
        }

        $id = (string)$entity->getId();
        $id = dechex($id);

        $configFile = ROOT_DIR . "/config/auto.config.php";
        if (file_exists($configFile)) {
            $config = include $configFile;
        } else {
            $config = [];
        }
        $config = \DeltaUtils\ArrayUtils::set($config, ["Articles", "Tags", "Dictionaries"], [$id]);
        $config = var_export($config, true);
        $config = "<?php" . PHP_EOL . "//Do not change this file!" . PHP_EOL . "//created automatically" . PHP_EOL . "return" . PHP_EOL . $config . ";" . PHP_EOL;

        file_put_contents($configFile, $config);
    }
}
