<?php
namespace common\models\agent;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%agent_auth_assignment}}".
 *
 * @property string $item_name
 * @property string $user_id
 * @property int $created_at
 *
 * @property AuthItem $itemName
 */
class AuthAssignment extends \common\models\common\BaseModel
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public static function tableName()
    {
        return '{{%agent_auth_assignment}}';
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function rules()
    {
        return [
            [
                [
                    'item_id',
                    'user_id'
                ],
                'required'
            ],
            [
                [
                    'item_id',
                    'created_at',
                    'user_id'
                ],
                'integer'
            ],
            [
                [
                    'item_id',
                    'user_id'
                ],
                'unique',
                'targetAttribute' => [
                    'item_id',
                    'user_id'
                ]
            ],
            [
                [
                    'item_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuthItem::class,
                'targetAttribute' => [
                    'item_id' => 'id'
                ]
            ]
        ];
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function attributeLabels()
    {
        return [
            'item_id' => '角色名称',
            'user_id' => 'User ID',
            'created_at' => '创建时间'
        ];
    }

    /**
     *
     * @param
     *            $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function finldByUserId($id)
    {
        return self::find()->where([
            'user_id' => $id
        ])
            ->with([
            'authItemChild',
            'addonsAuthItemChild'
        ])
            ->asArray()
            ->one();
    }

    /**
     *
     * @param
     *            $itemNames
     * @return array|null|ActiveRecord
     */
    public static function finldItemNames($itemNames)
    {
        return self::find()->where([
            'in',
            'item_id',
            $itemNames
        ])
            ->select('user_id')
            ->asArray()
            ->one();
    }

    /**
     * 根据用户ID获取权限名称
     *
     * @param
     *            $user_id
     * @return bool|mixed
     */
    public function getName($user_id)
    {
        if (! $user_id) {
            return false;
        }
        
        $model = $this::find()->where([
            'user_id' => $user_id
        ])->one();
        
        return $model ? $model->item_id : false;
    }

    /**
     * 关联权限名称
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::class, [
            'id' => 'item_id'
        ]);
    }

    /**
     * 关联权限列表
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChild()
    {
        return $this->hasMany(AuthItemChild::class, [
            'parent' => 'item_id'
        ]);
    }

    /**
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at'
                    ]
                ]
            ]
        ];
    }
}
