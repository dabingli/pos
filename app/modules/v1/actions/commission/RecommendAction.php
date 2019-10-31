<?php
namespace app\modules\v1\actions\commission;

use yii;
use yii\base\Action;
use common\models\entities\nestedSets\UserLink;
use common\models\entities\User;

class RecommendAction extends Action
{

    public function run()
    {
        $model = UserLink::findOne(Yii::$app->user->id);
        
        $childrenModel = $model->children(1);
        $childrenModel->andWhere([
            'a.agent_id' => Yii::$app->params['agentModel']->id
        ]);
        $childrenModel->orderBy([
            'user_id' => SORT_ASC
        ]);
        $childrenModel->alias('a');
        $childrenModel->joinWith([
            'user' => function ($q) {
                $q->alias('b');
                $q->select([
                    'id'
                ]);
                $q->andWhere([
                    'b.agent_id' => Yii::$app->params['agentModel']->id
                ]);
            }
        ], true, 'INNER JOIN');
        $childrenModel->select([
            'a.user_id',
            'b.id',
            'count' => 'count(*)',
            'authentication' => 'SUM(is_authentication=' . User::AUTH_YES . ')',
            'not_authentication' => 'SUM(is_authentication!=' . User::AUTH_YES . ')'
        ]);
        $data = $childrenModel->asArray()->one();
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $data
        ];
    }
}