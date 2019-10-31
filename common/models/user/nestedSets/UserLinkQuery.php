<?php
namespace common\models\user\nestedSets;

use creocoder\nestedsets\NestedSetsQueryBehavior;

class UserLinkQuery extends \yii\db\ActiveQuery
{

    public function behaviors()
    {
        return [
            NestedSetsQueryBehavior::className()
        ];
    }
}