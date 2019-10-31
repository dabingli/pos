<?php
namespace backend\modules\automation\controllers;

use yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use backend\modules\automation\controllers\BaseController;
use common\models\Crontab;

class CrontabsController extends BaseController
{

    /**
     * 定时任务
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        $model = Crontab::find();
        $model->andFilterWhere([
            'like',
            'name',
            $this->request->post('name')
        ]);
        $model->andFilterWhere([
            'like',
            'route',
            $this->request->post('route')
        ]);
        $limit = $this->request->post('limit');
        $offset = $this->request->post('offset');
        $model->offset($offset)->limit($limit);
        $data['total'] = $model->count();
        foreach ($model->all() as $val) {
            $crontabJobs = Yii::$app->crontabs->findJobsByGroupName(md5($val->id));
            $data['rows'][] = [
                'id' => $val->id,
                'name' => $val->name,
                'route' => $val->route,
                'crontab' => $val->crontab,
                'num' => sizeof($crontabJobs),
                'numing' => Yii::$app->crontabs->getJobsProcessCount(md5($val->id), $val->route),
                'remarks' => $val->remarks
            ];
        }
        return $this->asJson($data);
    }

    public function actionDel()
    {
        $db = Yii::$app->db;
        $beginTransaction = $db->beginTransaction();
        $model = Crontab::find();
        $model->andWhere([
            'id' => $this->request->post('id')
        ]);
        foreach ($model->all() as $m) {
            Yii::$app->crontabs->clearJobsByGroupName(md5($m->id));
            $m->delete();
        }
        $beginTransaction->commit();
        $this->message('删除成功', '', 'success');
        return $this->asJson([]);
    }

    public function actionAdd()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $data['html'] = $this->renderPartial('add');
        }
        
        return $this->asJson($data);
    }

    public function actionAddDo()
    {
        if (! $this->request->isPost) {
            
            return $this->redirect([
                'index'
            ]);
        }
        $model = new Crontab();
        if (empty($this->request->post('minutes'))) {
            $minutes = '*';
        } else {
            $minutes = $this->request->post('minutes');
        }
        if (empty($this->request->post('hours'))) {
            $hours = '*';
        } else {
            $hours = $this->request->post('hours');
        }
        if (empty($this->request->post('dayOfMonth'))) {
            $dayOfMonth = '*';
        } else {
            $dayOfMonth = $this->request->post('dayOfMonth');
        }
        if (empty($this->request->post('months'))) {
            $months = '*';
        } else {
            $months = $this->request->post('months');
        }
        if (empty($this->request->post('dayOfWeek'))) {
            $dayOfWeek = '*';
        } else {
            $dayOfWeek = $this->request->post('dayOfWeek');
        }
        $crontab = $minutes . ' ' . $hours . ' ' . $dayOfMonth . ' ' . $months . ' ' . $dayOfWeek;
        $model->load([
            'name' => $this->request->post('name'),
            'route' => $this->request->post('route'),
            'remarks' => $this->request->post('remarks'),
            'crontab' => $crontab
        ], '');
        $db = Yii::$app->db;
        $beginTransaction = $db->beginTransaction();
        if (! $model->save()) {
            $msgText = $this->multiErrors2Msg($model->errors);
            return $this->redirect($this->message($msgText, Url::toRoute([
                'index'
            ]), 'warning'));
        }
        $jobsCount = intval(Yii::$app->request->post('num', 0));
        if ($jobsCount > 60) {
            $jobsCount = 60;
        }
        if (0 >= $jobsCount) {
            Yii::$app->crontabs->clearJobsByGroupName(md5($model->id));
            $beginTransaction->commit();
            return $this->redirect([
                'index'
            ]);
        }
        Yii::$app->crontabs->persistByGroupName(md5($model->id), $model->route, $jobsCount, $minutes, $hours, $dayOfMonth, $months, $dayOfWeek);
        $beginTransaction->commit();
        return $this->redirect($this->message('添加成功', Url::toRoute([
            'index'
        ]), 'success'));
    }

    public function actionEdit()
    {
        $data['html'] = '';
        if ($this->request->isAjax) {
            $model = Crontab::findOne([
                'id' => $this->request->post('id')
            ]);
            $jobs = [];
            $crontabJobs = Yii::$app->crontabs->findJobsByGroupName(md5($model->id));
            foreach ($crontabJobs as $crontabJob) {
                $job = new \stdClass();
                $job->datetimeFormat = sprintf('%s %s %s %s %s', $crontabJob->minutes, $crontabJob->hours, $crontabJob->dayOfMonth, $crontabJob->months, $crontabJob->dayOfWeek);
                $job->taskCommandLine = $crontabJob->taskCommandLine;
                $job->comments = $crontabJob->comments;
                $jobs[] = $job;
            }
            $data['html'] = $this->renderPartial('edit', [
                'model' => $model,
                'jobs' => $jobs,
                'num' => sizeof(Yii::$app->crontabs->findJobsByGroupName(md5($model->id))),
                'numing' => Yii::$app->crontabs->getJobsProcessCount(md5($model->id), $model->route)
            ]);
        }
        
        return $this->asJson($data);
    }

    public function actionEditDo()
    {
        if (! $this->request->isPost) {
            
            return $this->redirect([
                'index'
            ]);
        }
        $model = Crontab::findOne([
            'id' => $this->request->post('id')
        ]);
        $jobsCount = intval(Yii::$app->request->post('num', 0));
        if ($jobsCount > 60) {
            $jobsCount = 60;
        }
        $db = Yii::$app->db;
        $beginTransaction = $db->beginTransaction();
        $model->save();
        if (0 >= $jobsCount) {
            Yii::$app->crontabs->clearJobsByGroupName(md5($model->id));
            $beginTransaction->commit();
            return $this->redirect($this->message('编辑成功', Url::toRoute([
                'index'
            ]), 'success'));
        }
        Yii::$app->crontabs->persistByGroupName(md5($model->id), $model->route, $jobsCount);
        $beginTransaction->commit();
        return $this->redirect($this->message('编辑成功', Url::toRoute([
            'index'
        ]), 'success'));
    }
}