<?php
namespace frontend\modules\sys\controllers;

use Yii;
use yii\data\Pagination;
use common\models\agent\ActionLog;

/**
 * 日志控制器
 *
 * Class LogController
 *
 * @package backend\modules\sys\controllers
 */
class LogController extends \frontend\controllers\MController
{

    /**
     * 行为日志
     *
     * @return string
     */
    public function actionAction()
    {
        $data = ActionLog::find();
        $pages = new Pagination([
            'totalCount' => $data->count(),
            'pageSize' => $this->pageSize
        ]);
        $models = $data->offset($pages->offset)
            ->with([
            'manager'
        ])
            ->andWhere([
            'agent_id' => $this->agentId
        ])
            ->limit($pages->limit)
            ->orderBy('id desc')
            ->all();
        
        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages
        ]);
    }

    /**
     * 行为日志详情
     *
     * @param
     *            $id
     * @return string
     */
    public function actionActionView($id)
    {
        $model = ActionLog::find()->where([
            'id' => $id
        ])->one();
        return $this->renderAjax($this->action->id, [
            'model' => $model
        ]);
    }
}