<?php
namespace common\widgets;

use yii;
use yii\base\Widget;
use yii\helpers\Html;
use common\models\common\Provinces as Region;

class RegionWidget extends Widget
{

    public $region_id;

    public $name;

    public $options;

    public $parent_id;

    public function run()
    {
        if ($this->parent_id) {
            $data = Region::find()->andWhere([
                'pid' => Region::find()->andWhere([
                    'id' => $this->parent_id
                ])
                    ->indexBy('pid')
                    ->select([
                        'id'
                    ])
                    ->column()
            ])
                ->indexBy('id')
                ->select([
                    'title'
                ])
                ->column();
        } elseif ($this->parent_id === null) {

            $data = Region::find()->andWhere([
                'pid' => 0
            ])
                ->indexBy('id')
                ->select([
                    'title'
                ])
                ->column();
        } elseif ($this->parent_id === false) {
            $data = [];
        }

        return Html::dropDownList($this->name, $this->region_id, [
                '' => '请选择'
            ] + $data, $this->options);
    }
}