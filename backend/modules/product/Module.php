<?php
namespace backend\modules\product;

class Module extends \yii\base\Module
{

    public function init()
    {
        parent::init();
        
        $this->params['perm'] = require (__DIR__ . '/config/perm.php');
    }
}