<?php
namespace common\services;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class AgentMenu extends ActiveRecord
{

    const START = 1;

    const STOP = 0;

    const DEL = -1;

    public static function tableName()
    {
        return '{{%agent_menu}}';
    }

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ]
        ];
    }

    public function rules()
    {
        return [
            [
                [
                    'name'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'route',
                    'parent_id',
                    'order',
                    'remarks',
                    'icon'
                ],
                'safe'
            ],
            [
                'parent_id',
                'default',
                'value' => 0
            ],
            [
                'is_show',
                'default',
                'value' => self::SHOW
            ],
            [
                [
                    'order'
                ],
                'default',
                'value' => 1
            ],
            [
                'remarks',
                'default',
                'value' => ''
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '菜单名称',
            'route' => 'URL地址',
            'parent_id' => '上级菜单',
            'order' => '排序',
            'remarks' => '备注',
            'updated_at' => '修改时间',
            'created_at' => '添加时间'
        ];
    }

    public function afterDelete()
    {
        $arr = self::findAll([
            'parent_id' => $this->id
        ]);
        foreach ($arr as $m) {
            $m->delete();
        }
        return parent::afterDelete();
    }
}