<?php
namespace common\services\frontend;

use yii;
use common\models\user\UserSettlement;

class UserSettlementForm extends UserSettlement
{

    public function rules()
    {
        $rules = parent::rules();
        unset($rules['min']);
//        unset($rules['max']);
        unset($rules['cash_money']);
        return $rules;
    }
}