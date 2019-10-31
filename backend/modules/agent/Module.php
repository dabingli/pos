<?php
namespace backend\modules\agent;

use yii;

class Module extends \yii\base\Module
{

    public function init()
    {
        parent::init();
        
        Yii::configure($this, require __DIR__ . '/config/config.php');
        $this->modules = [
            'rbac' => [
                'class' => 'backend\modules\agent\modules\rbac\Module'
            ]
        ];
    }
}