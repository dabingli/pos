<?php
namespace app\modules\v1\actions\commission;

use yii;
use yii\base\Action;
use common\models\entities\nestedSets\UserLink;
use common\models\entities\User;
use common\models\entities\Order;
use common\models\entities\UserBankCard;

/**
 *
 * @author Administrator
 *        
 */
class CashListAction extends Action
{

    public $page = 1;

    public $limit;

    public $status;

    public function run()
    {
        $model = Order::find();
        $model->andWhere([
            'type' => Order::CASH
        ]);
        $model->andWhere([
            'user_id' => Yii::$app->user->id,
            'agent_id' => Yii::$app->params['agentModel']->id
        ]);
        $model->andFilterWhere([
            'status' => $this->status
        ]);
        $offset = ($this->page - 1) * $this->limit;
        $model->limit($this->limit)->offset($offset);
        $model->orderBy([
            'id' => SORT_DESC
        ]);
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $model->all()
        ];
    }
}