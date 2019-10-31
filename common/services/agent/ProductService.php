<?php
namespace common\services\agent;

use common\models\agent\AgentProductType;
use Yii;
use common\services\Service;
use common\models\product\Product;
use common\models\product\ProductType;

class ProductService extends Service
{

    /**
     * @getExpireTimeProduct 获取所有到期机具
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getExpireTimeProduct()
    {
        //查询所有过期未激活的机具
        return Product::find()
            ->where([
                '<=',
                'expire_time',
                time() - (3600 * 24)
            ])
            ->andWhere([
                'activate_status' => Product::NO,
                'frozen_status' => Product::NO_FROZEN
            ])
            ->asArray()
            ->all();
    }

    /**
     * @getAgentProductTypeInfo 获取代理商对应的机具类型信息
     * @param null $agentId
     * @param null $productTypeId
     * @return null|ProductType
     */
    public function getAgentProductTypeInfo($agentId=null, $productTypeId=null)
    {
        if( !$agentId || !$productTypeId ){
            return null;
        }

        $productTypeModel = new AgentProductType();
        return $productTypeModel->findOne(['agent_id'=>$agentId, 'id'=>$productTypeId]);
    }


}