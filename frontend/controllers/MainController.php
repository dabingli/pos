<?php
namespace frontend\controllers;

use common\models\agent\Agent;
use common\services\sys\Sys;
use Yii;
use common\models\agent\AgentMenuCate as MenuCate;
use common\helpers\DebrisHelper;

class MainController extends MController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    /**
     * 系统首页
     *
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        // 判断是否手机
        Yii::$app->params['isMobile'] = DebrisHelper::isMobile();
        $agent = Agent::findOne([
            'id' => Yii::$app->user->identity->agent_id
        ]);
        $days = ($agent->expired_time - time())/3600/24;
        
        // 拉取公告
        Yii::$app->services->agent->notify->pullAnnounce(Yii::$app->user->id);
        // 获取当前通知
        list ($notify, $notifyPage) = Yii::$app->services->sys->notify->getUserNotify(Yii::$app->user->id);
        
        return $this->renderPartial('index', [
            'menuCates' => MenuCate::getList(),
            'manager' => Yii::$app->user->identity,
            'notify' => $notify,
            'notifyPage' => $notifyPage,
            'days' => $days
        ]);
    }

    /**
     * 系统主页
     *
     * @return string
     */
    public function actionSystem()
    {
//        $system = new Sys();
//        $data['transaction'] = $system->getHanding();
        return $this->render('system', []);
    }

    /**
     * 清理缓存
     */
    public function actionClearCache()
    {
        // 商家没有必要去清缓存
        return $this->render('clear-cache', [
            'result' => $result
        ]);
        // 删除后台文件缓存
        $result = Yii::$app->cache->flush();
        
        // 删除备份缓存
        $path = Yii::$app->params['dataBackupPath'];
        print_r($path);
        die();
        $lock = realpath($path) . DIRECTORY_SEPARATOR . Yii::$app->params['dataBackLock'];
        array_map("unlink", glob($lock));
        
        return $this->render('clear-cache', [
            'result' => $result
        ]);
    }
}