<?php
namespace app\modules\v1\controllers;

use yii;
use yii\filters\auth\HttpBearerAuth;
use common\models\user\User;
use common\models\app\News;
use function GuzzleHttp\json_encode;

class NewsController extends BaseActiveController
{

    public $modelClass = 'common\models\app\AppMessage';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className()
        ];
        
        return $behaviors;
    }

    public function actionList()
    {
        $model = News::find();
        $model->andWhere([
            'is_delete' => News::NOT_DELETE
        ]);
        $limit = $this->request->post('limit') ? $this->request->post('limit') : 10;
        $offset = (($this->request->post('page') > 1 ? $this->request->post('page') : 1) - 1) * $limit;
        $model->limit($limit);
        $model->offset($offset);
        $model->orderBy([
            'id' => SORT_DESC
        ]);
        $model->asArray();
        $data = $model->all();
        foreach ($data as &$m) {
            $m['images'] = json_decode($m['images'], true);
        }
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $data
        ];
    }

    public function actionFind()
    {
        $model = News::findOne([
            'id' => $this->request->post('id'),
            'is_delete' => News::NOT_DELETE
        ]);
        if (isset($model->images)) {
            $model->images = json_decode($model->images, true);
        }
        
        return [
            'status' => 0,
            'code' => 200,
            'message' => [],
            'data' => $model
        ];
    }
}