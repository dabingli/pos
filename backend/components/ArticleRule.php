<?php
namespace backend\components;

use yii\rbac\Rule;

class ArticleRule extends Rule
{

    public $name = 'article';

    public function execute($user, $item, $params)
    {
        die;
        return false;
    }
}