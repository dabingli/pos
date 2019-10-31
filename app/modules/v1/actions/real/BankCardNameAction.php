<?php
namespace app\modules\v1\actions\real;

use common\library\api\bankCardName\Ccdcapi;
use common\models\user\BankCard;
use yii\base\Action;


class BankCardNameAction extends Action
{

    public $cardNo;

    public function run()
    {   
        $model = new Ccdcapi();
        $model->load([
            'cardNo' => $this->cardNo,
        ], '');
        
        if (! $model->validate()) {
            return [
                'status' => 0,
                'code' => 0,
                'data' => []
            ];
        }
        $res = $model->send();
        if (!$res['validated']) {
            return [
                'status' => 0,
                'code' => 0,
                'data' => []
            ];
        }
        $modelBank = BankCard::findOne(['bank'=>$res['bank'],'status'=>1]);
        
        if(empty($modelBank))
        {
            return [
                'status' => 0,
                'code' => 0,
                'data' => ''
            ];
        }

        return [
            'status' => 0,
            'code' => 200,
            'data' => [
                'bank'=>$modelBank['bank'],
                'name'=>$modelBank['name']
            ]
        ];
    }
}