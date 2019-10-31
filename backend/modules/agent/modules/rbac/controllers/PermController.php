<?php
namespace backend\modules\agent\modules\rbac\controllers;

use yii;
use yii\data\Pagination;
use yii\db\Query;
use common\components\rbac\frontend\DbManager;
use common\models\entities\MechanismPermsRoutes;
use common\models\entities\MechanismPerm;
use common\models\services\StoreMenuServices;

class PermController extends \backend\modules\agent\modules\rbac\controllers\BaseController
{

    /**
     * 帐号管理显示页面
     */
    public function actionIndex($id = '')
    {
        //echo Yii::getAlias('@common');die;
        $perm = MechanismPerm::findOne($id);
        if (empty($perm)) {
            $perm = new MechanismPerm();
        }
        $routes = (new Query())->select('route')
            ->from('mechanism_perms_routes')
            ->where([
            'perm_id' => $id
        ])
            ->column();
        $routes = implode("\n", $routes);
        $storeMenuServicesModel = new StoreMenuServices();
        $menu = $storeMenuServicesModel->getRbacTree(0);
        return $this->render('index', [
            'perm' => $perm,
            'routes' => $routes,
            'menu' => $menu
        ]);
    }

    /**
     * 新增界面
     */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        
        if (empty($post)) {
            return $this->redirect([
                'index'
            ]);
        }
        $name = isset($post['name']) ? $post['name'] : '';
        $routes = isset($post['routes']) ? $post['routes'] : '';
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $connection->createCommand()
                ->insert('mechanism_perm', [
                'name' => $name
            ])
                ->execute();
            $permId = $connection->getLastInsertID();
            $routes = explode("\n", $routes);
            foreach ($routes as $route) {
                if (! empty(trim($route))) {
                    $data[] = [
                        $permId,
                        trim($route)
                    ];
                }
            }
            if (! empty($data)) {
                $connection->createCommand()
                    ->batchInsert('mechanism_perms_routes', [
                    'perm_id',
                    'route'
                ], $data)
                    ->execute();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('danger', '数据保存失败，请重新操作');
            $transaction->rollBack();
            return $this->redirect([
                'index'
            ]);
        }
        Yii::$app->session->setFlash('success', '权限添加完成');
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * 更新执行动作
     */
    public function actionUpdate()
    {
        $post = Yii::$app->request->post();
        $model = MechanismPerm::findOne([
            'id' => $this->request->post('id')
        ]);
        $model->load([
            'name' => $this->request->post('name')
        ]);
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $routes = isset($post['routes']) ? $post['routes'] : '';
        try {
            if ($model->save()) {
                MechanismPermsRoutes::deleteAll([
                    'perm_id' => $model->id
                ]);
                if ($routes) {
                    $routes = explode("\n", $routes);
                    foreach ($routes as $route) {
                        if (! empty(trim($route))) {
                            $data[] = [
                                $model->id,
                                trim($route)
                            ];
                        }
                    }
                    if (! empty($data)) {
                        $connection->createCommand()
                            ->batchInsert('mechanism_perms_routes', [
                            'perm_id',
                            'route'
                        ], $data)
                            ->execute();
                    }
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('danger', '数据保存失败，请重新操作');
            $transaction->rollBack();
            return $this->redirect([
                'index'
            ]);
        }
        Yii::$app->session->setFlash('success', '权限修改完成');
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * 删除执行动作
     */
    public function actionDelete()
    {
        $id = $this->request->post('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $connection->createCommand()
                ->delete('mechanism_perms_routes', [
                'perm_id' => $id
            ])
                ->execute();
            
            $connection->createCommand()
                ->delete('mechanism_perm', [
                'perm_id' => $id
            ])
                ->execute();
            Yii::$app->session->setFlash('success', '权限删除完成');
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('danger', '数据保存失败，请重新操作');
            $transaction->rollBack();
        }
        
        return $this->asJson([]);
    }

    public function actionList()
    {
        $model = MechanismPerm::find();
        $model->alias('a');
        $model->joinWith([
            'mechanismPermsRoutes' => function ($q) {
                $q->alias('b');
            }
        ]);
        $model->orderBy([
            'a.id' => SORT_DESC
        ]);
        $keyword = $this->request->post("keyword");
        $model->orFilterWhere([
            'or',
            [
                'like',
                'a.name',
                $keyword
            ],
            [
                'like',
                'b.route',
                $keyword
            ]
        ]);
        $perms = $model->offset($this->request->post('offset'))
            ->limit($this->request->post('limit'))
            ->all();
        $data['total'] = $model->count();
        $data['rows'] = [];
        foreach ($perms as $perm) {
            $routes = implode("<br/>", array_column($perm->mechanismPermsRoutes, 'route'));
            $data['rows'][] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'route' => $routes
            ];
        }
        return $this->asJson($data);
    }
}