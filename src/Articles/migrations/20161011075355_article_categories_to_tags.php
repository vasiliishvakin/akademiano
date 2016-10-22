<?php

use Phinx\Migration\AbstractMigration;
use DeltaPhp\TagsDictionary\Entity\Tag;
use Articles\Model\Article;
use Articles\Model\ArticleTagRelation;
use DeltaPhp\TagsDictionary\Entity\DictionaryTagRelation;

class ArticleCategoriesToTags extends AbstractMigration
{
    public function up()
    {
        /** @var \DeltaCore\Application $app */
        $app = include __DIR__ . "/../App/bootstrap.php";
        $app->init();


        $sql = "select ac.id category_id, ac.name category, ar.id article_id, ar.title article_title from article_categories ac left join article_categories_matrix acm on acm.category=ac.id
left join articles ar on ar.old_id=acm.article";
        $categoriesRawData = $this->fetchAll($sql);

        $categoriesData = [];
        foreach ($categoriesRawData as $row) {
            if (!empty($row["article_id"])) {
                $categoriesData[$row["category_id"]]["articles"][] = $row["article_id"];
            }
            if (!isset($categoriesData[$row["category_id"]]["category"])) {
                $categoriesData[$row["category_id"]]["category"] = [
                    "old_id" => $row["category_id"],
                    "title" => $row["category"],
                ];
            }
        }

        /** @var \DeltaPhp\Operator\EntityOperator $operator */
        $operator = $app["Operator"];

        $articleTagsHelper = new \Articles\Model\ArticleTags($operator, $app->getConfig("Articles"));
        $dictionary = $articleTagsHelper->getDictionary();


        foreach ($categoriesData as $categoryRow) {
            /** @var Tag $tag */
            $tag = $operator->create(Tag::class);
            $tag->setTitle($categoryRow["category"]["title"]);
            usleep(mt_rand(0, 1000000));
            $operator->save($tag);

            //tags_dict_relation
            /** @var DictionaryTagRelation $atRelation */
            $dtRelation = $operator->create(DictionaryTagRelation::class);
            $dtRelation->setFirst($dictionary);
            $dtRelation->setSecond($tag);
            usleep(mt_rand(0, 1000000));
            $operator->save($dtRelation);

            if (isset($categoryRow["articles"]) && count($categoryRow["articles"] > 0)) {
                foreach ($categoryRow["articles"] as $articleId) {
                    $article = $operator->get(Article::class, $articleId);
                    if (!$article) {
                        throw new RuntimeException("Article with id #{$articleId} not found");
                    }
                    /** @var ArticleTagRelation $atRelation */
                    $atRelation = $operator->create(ArticleTagRelation::class);
                    $atRelation->setFirst($article);
                    $atRelation->setSecond($tag);
                    usleep(mt_rand(0, 1000000));
                    $operator->save($atRelation);
                }
            }
        }
    }
}
