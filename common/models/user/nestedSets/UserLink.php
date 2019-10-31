<?php
namespace common\models\user\nestedSets;

use creocoder\nestedsets\NestedSetsBehavior;
use common\models\user\User;
use yii\behaviors\TimestampBehavior;

class UserLink extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return '{{%user_link}}';
    }

    public function rules()
    {
        return [
            [
                'agent_id',
                'integer'
            ],
            [
                'agent_id',
                'required',
                'message' => '{attribute}不能为空'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'agent_id' => '商家ID'
        ];
    }

    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth'
            ],
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false
            ]
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL
        ];
    }

    public static function find()
    {
        return new UserLinkQuery(get_called_class());
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }
}