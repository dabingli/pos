<?php
namespace backend\modules\agent\controllers;

use yii\data\Pagination;
use common\components\CurdTrait;
use common\models\agent\AgentMenuCate as MenuCate;
use backend\modules\agent\controllers\BaseController;

class MenuCateController extends BaseController
{
    use CurdTrait;

    /**
     *
     * @var
     *
     */
    public $modelClass = 'common\models\agent\AgentMenuCate';

    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $data = MenuCate::find();
        $pages = new Pagination([
            'totalCount' => $data->count(),
            'pageSize' => $this->pageSize
        ]);
        $models = $data->offset($pages->offset)
            ->orderBy('sort asc')
            ->limit($pages->limit)
            ->all();
        
        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages
        ]);
    }
}