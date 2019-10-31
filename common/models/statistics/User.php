<?php
namespace common\models\statistics;

use Yii;
use common\models\Transaction;
use common\models\Profit;

class User extends \common\models\user\User
{

    /**
     * 交易总金额
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTotalMoneyOne()
    {
        return $this->hasOne(Transaction::className(), [
            'user_id' => 'id'
        ]);
    }

    /**
     * 分润总金额
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfitMoneyOne()
    {
        return $this->hasOne(Profit::className(), [
            'user_id' => 'id'
        ]);
    }

    public function getSonCount()
    {
        return $this->hasOne(User::className(), [
            'parent_id' => 'id'
        ]);
    }
}
