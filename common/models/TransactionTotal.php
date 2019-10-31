<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\user\User;
use common\models\user\nestedSets\UserLink;

class TransactionTotal extends \common\models\common\BaseModel
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public $rank;

    public static function tableName()
    {
        return '{{%transaction_total}}';
    }

    public function behaviors()
    {
        return [
            'time' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false
            ]
        ];
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
                    'agent_id',
                    'user_id'
                
                ],
                'required',
                'message' => '{attribute}不能为空'
            ],
            [
                [
                    'id',
                    'agent_id',
                    'user_id',
                    'num',
                    'total_money'
                ],
                'safe'
            ],
            [
                'total_money',
                'double'
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
            'agent_id' => '商家',
            'user_id' => '用户id',
            'created_at' => '创建时间',
            'num' => '激活数量',
            'total_money' => '交易金额'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }

    /**
     * @getMyTotalField 获取我的统计信息，如我的累计交易金额或激活机具数【该统计信息还包括所有下级的累计信息】
     * @param int $user_id
     * @param string $fieldName
     * @return null
     * @throws \yii\db\Exception
     */
    public static function getMyTotalField($user_id=0, $fieldName='total_money')
    {
        if ( empty($user_id) ) return null;
        $userLink = UserLink::findOne(['user_id'=>$user_id]);
        $totalSql = "SELECT SUM(tt.{$fieldName}) as total FROM user_link ll LEFT JOIN transaction_total tt ON  tt.user_id = ll.user_id WHERE ll.tree = {$userLink['tree']} AND ll.lft >= {$userLink['lft']} AND ll.rgt <= {$userLink['rgt']}";
        $total = Yii::$app->db->createCommand($totalSql)->queryOne();
        return $total ? $total['total'] : null;
    }

    /**
     * @getMyRank 获取我的交易金额或激活机具数排名 包括所有下级的统计信息的排名
     * @param int $user_id 当$total不存在时。必传
     * @param int $agent_id
     * @param string $fieldName
     * @param int $total 我的累计交易金额或激活机具数
     * @return int|null
     * @throws \yii\db\Exception
     */
    public static function getMyTotalFieldRank($user_id=0, $agent_id=0, $fieldName='total_money', $total=0)
    {
        $total = $total ? $total : self::getMyTotalField($user_id, $fieldName);
        if( empty($total) ){
            return null;
        }

        $mySql = "SELECT COUNT(1) as rank FROM ( ";
        $mySql .= " SELECT (SELECT SUM(tt.{$fieldName}) FROM user_link ll LEFT JOIN transaction_total tt ON  tt.user_id = ll.user_id WHERE ll.tree = l.tree AND ll.lft >= l.lft AND ll.rgt <= l.rgt) AS total ";
        $mySql .= " FROM transaction_total t ";
        $mySql .= " LEFT JOIN user_link l ON l.user_id=t.user_id ";
        if (!empty($agent_id)) $mySql .= " WHERE t.agent_id = {$agent_id} ";
        $mySql .= " ) AS m WHERE m.total > {$total}";
        $myRank = Yii::$app->db->createCommand($mySql)->queryOne();

        return $myRank ? $myRank['rank'] + 1 : null;
    }

    /**
     * @getRankList 获取交易排行 自己的交易金额 + 所有下级的交易金额 排行
     * @param int $agent_id 存在则该一级代理商排行，空则总排行
     * @param string $fieldName 排行的字段名，transaction_total表的某字段
     * @param string $orderBy 排序方式
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getRankList($agent_id=0, $fieldName='total_money', $orderBy='DESC', $page=0, $limit=10)
    {
        $index = $page * $limit;

        $sql = "SELECT t.id,t.agent_id,t.user_id,t.created_at,u.avatar,u.mobile ";
        $sql .= ", (SELECT SUM(tt.{$fieldName}) FROM user_link ll LEFT JOIN transaction_total tt ON tt.user_id = ll.user_id WHERE ll.tree = l.tree AND ll.lft >= l.lft AND ll.rgt <= l.rgt) AS {$fieldName} ";
        $sql .= " FROM transaction_total t ";
        $sql .= " LEFT JOIN user u ON u.id=t.user_id ";
        $sql .= " LEFT JOIN user_link l ON l.user_id=t.user_id ";
        if (!empty($agent_id)) $sql .= " WHERE t.agent_id = {$agent_id} ";
        $sql .= " ORDER BY {$fieldName} {$orderBy}";
        $sql .= " LIMIT {$index}, {$limit}";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

}
