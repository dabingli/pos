<?php
namespace common\models\agent;

use common\enums\StatusEnum;
use Yii;

/**
 * This is the model class for table "{{%agent_notify}}".
 *
 * @property string $id 主键
 * @property string $title 标题
 * @property string $content 消息内容
 * @property int $type 消息类型[1:公告;2:提醒;3:信息(私信)
 * @property int $target_id 目标id
 * @property string $target_type 目标类型
 * @property int $target_display 接受者是否删除
 * @property string $action 动作
 * @property int $view 浏览量
 * @property int $sender_id 发送者id
 * @property int $sender_display 发送者是否删除
 * @property int $sender_withdraw 是否撤回 0是撤回
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class Notify extends \common\models\common\BaseModel
{

    // 消息类型
    const TYPE_ANNOUNCE = 1;
 // 公告
    const TYPE_REMIND = 2;
 // 提醒
    const TYPE_MESSAGE = 3;
 // 消息
                            
    // 消息类型说明
    public static $typeExplain = [
        self::TYPE_ANNOUNCE => '公告',
        self::TYPE_REMIND => '提醒',
        self::TYPE_MESSAGE => '私信'
    ];

    // 提醒关联的目标类型
    public static $targetType = [
        'PRODUCT' => 'product', // 产品
        'POST' => 'post' // 文章
    ];

    // 提醒关联的动作
    public static $action = [
        'COMMENT' => 'comment', // 评论
        'LIKE' => 'like' // 喜欢
    ];

    // 订阅原因对应订阅事件
    public static $reasonAction = [
        'create_product' => [
            'comment',
            'like'
        ], // 创建产品
        'like_product' => [
            'comment'
        ], // 喜欢产品
        'like_post' => [
            'comment'
        ] // 喜欢文章
    ];

    // 默认订阅配置
    public static $defaultSubscriptionConfig = [
        'comment' => true, // 评论
        'like' => true // 喜欢
    ];

    /**
     *
     * {@inheritdoc}
     *
     */
    public static function tableName()
    {
        return '{{%agent_notify}}';
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
                    'content'
                ],
                'string'
            ],
            [
                [
                    'type',
                    'target_id',
                    'target_display',
                    'view',
                    'sender_id',
                    'sender_display',
                    'sender_withdraw',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'integer'
            ],
            [
                [
                    'title'
                ],
                'string',
                'max' => 150
            ],
            [
                [
                    'target_type',
                    'action'
                ],
                'string',
                'max' => 100
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
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'type' => '类别',
            'target_id' => '触发id',
            'target_type' => '触发类别',
            'target_display' => '触发事件',
            'action' => '触发方法',
            'view' => '浏览量',
            'sender_id' => 'Sender ID',
            'sender_display' => 'Sender Display',
            'sender_withdraw' => 'Sender Withdraw',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    /**
     * 关联发送用户
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSenderForManager()
    {
        return $this->hasOne(Manager::class, [
            'id' => 'sender_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotifyManager()
    {
        return $this->hasOne(NotifyManager::class, [
            'notify_id' => 'id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeassageManager()
    {
        return $this->hasOne(NotifyManager::class, [
            'notify_id' => 'id'
        ])->with('manager');
    }

    /**
     *
     * @param bool $insert            
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->status <= StatusEnum::DISABLED) {
            NotifyManager::updateAll([
                'status' => $this->status
            ], [
                'notify_id' => $this->id
            ]);
        }
        
        return parent::beforeSave($insert);
    }
}