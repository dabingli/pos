<?php
namespace console\controllers;

use yii;
use yii\console\Controller;
use common\components\oss\Aliyunoss;

class TestController extends Controller
{

    public function actionTest()
    {
        echo 1;
        //file_put_contents('/home/www/dian_pos/t.txt', date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
    }
}