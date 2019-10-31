<?php
namespace app\modules\v1;

class Module extends \yii\base\Module
{

    public function init()
    {
        parent::init();
        
        $this->modules = [
            'agent' => [
                // 代理商
                'class' => 'app\modules\v1\modules\agent\Module'
            ]
        ];
    }
}