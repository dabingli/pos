<?php
namespace backend\modules\sys\controllers;

use Yii;
use common\enums\StatusEnum;
use common\components\CurdTrait;
use common\models\common\SearchModel;
use common\models\sys\Notify;
//use backend\modules\sys\models\NotifyAnnounceForm;

/**
 * 公告
 *
 * Class NotifyAnnounce
 * @package backend\modules\sys\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyAnnounceController extends SController
{
    use CurdTrait;

    /**
     * @var string
     */
    public $modelClass = "common\models\sys\Notify";

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => Notify::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['type' => Notify::TYPE_ANNOUNCE]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);
        $model->type = Notify::TYPE_ANNOUNCE;
        $model->sender_id = Yii::$app->user->id;

        if ($model->load($request->post()) && $model->save())
        {
            return $this->message('操作成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || empty(($model = Notify::findOne($id))))
        {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }
}