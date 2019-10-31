<?php
namespace common\services;

use yii;
use common\services\AgentMenu as StoreMenuModel;
use common\models\agent\Agent;

class StoreMenuServices
{

    protected $data;

    public function getData($data, $parentId = 0)
    {
        $datas = [];
        foreach ($data as $k => $val) {
            if ($val['parent_id'] == $parentId) {
                $d = [];
                $d['text'] = $val['name'];
                $d['after_html'] = '<span class="button_z"><button type="button" class="btn btn btn-info btn-xs" onclick="edit(' . $val['id'] . ');">编辑</button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" onclick="del(' . $val['id'] . ');">删除</button></span>';
                unset($data[$k]);
                $nodes = $this->getData($data, $val['id']);
                if (! empty($nodes)) {
                    $d['nodes'] = $nodes;
                }
                $datas[] = $d;
            }
        }
        return $datas;
    }

    public function getDataTree($parentId = 0)
    {
        $this->data = StoreMenuModel::find()->andWhere([
            'status' => StoreMenuModel::START
        ])
            ->orderBy([
            'order' => SORT_ASC,
            'id' => SORT_ASC
        ])
            ->asArray()
            ->all();
        $datas = $this->getData($this->data);
        return $datas;
    }

    public function getRbacTree()
    {
        $this->data = StoreMenuModel::find()->andWhere([
            'status' => StoreMenuModel::START
        ])
            ->orderBy([
            'sort' => SORT_ASC,
            'id' => SORT_ASC
        ])
            ->asArray()
            ->all();
        $datas = $this->getRbacData($this->data);
        return $datas;
    }

    public function getRbacData($data, $parentId = 0)
    {
        $datas = [];
        foreach ($data as $k => $val) {
            if ($val['parent_id'] == $parentId) {
                $d['text'] = $val['name'];
                $d['after_html'] = '';
                unset($data[$k]);
                $nodes = $this->getRbacData($data, $val['id']);
                if (! empty($nodes)) {
                    $d['nodes'] = $nodes;
                }
                $datas[] = $d;
            }
        }
        return $datas;
    }

    public function getSelectData()
    {
        $this->data = StoreMenuModel::find()->andWhere([
            'status' => StoreMenuModel::START
        ])
            ->orderBy([
            'sort' => SORT_ASC,
            'id' => SORT_ASC
        ])
            ->asArray()
            ->all();
        return ($this->getSelectTree($this->data));
        die();
        return;
    }

    public function getSelectTree($data, $parentId = 0, $lv = 0)
    {
        $datas = [];
        foreach ($data as $k => $val) {
            if ($val['parent_id'] == $parentId) {
                $select = '';
                for ($i = 0; $i < $lv; $i ++) {
                    $select .= '&nbsp;&nbsp;';
                }
                $select = $select . '├ ';
                $d['name'] = $select . $val['name'];
                $d['id'] = $val['id'];
                unset($data[$k]);
                $datas[] = $d;
                $nodes = $this->getSelectTree($data, $val['id'], $lv + 1);
                if (! empty($nodes)) {
                    foreach ($nodes as $va) {
                        $datas[] = [
                            'id' => $va['id'],
                            'name' => $va['name']
                        ];
                    }
                }
            }
        }
        return $datas;
    }

    public function getUserData(Agent $agent, $data, $parentId = 0)
    {
        $datas = [];
        foreach ($data as $k => $val) {
            if ($val['pid'] == $parentId) {
                $d['text'] = $val['title'];
                $d['id'] = $val['id'];
                if (in_array($val['id'], $agent->getMenus())) {
                    $d['state']['checked'] = true;
                } else {
                    $d['state']['checked'] = false;
                }
                unset($data[$k]);
                $nodes = $this->getUserData($agent, $data, $val['id']);
                if (! empty($nodes)) {
                    $d['nodes'] = $nodes;
                }
                $datas[] = $d;
            }
        }
        return $datas;
    }

    public function getUserTree(Agent $agent)
    {
        $this->data = StoreMenuModel::find()->andWhere([
            'status' => StoreMenuModel::START
        ])
            ->orderBy([
            'sort' => SORT_ASC,
            'id' => SORT_ASC
        ])
            ->asArray()
            ->all();
        $datas = $this->getUserData($agent, $this->data);
        return $datas;
    }
}