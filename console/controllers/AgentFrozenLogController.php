<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\services\agent\AgentFrozenLogService;
use common\services\agent\UserService;
use common\services\agent\ProductService;


class AgentFrozenLogController extends Controller
{

    // 检查到期机具 并生成冻结记录
    public function actionCheckExpired()
    {

        // 获取过期未激活机具
        $productService = new ProductService();
        $productList = $productService->getExpireTimeProduct();
        if(empty($productList)){
            Yii::warning('没有到期的机具信息');
            return false;
        }

        $userService = new UserService();
        $agentFrozenLogService = new AgentFrozenLogService();

        $userInfo = $productType = null;
        // 遍历处理到期机具
        foreach ($productList as $key=>$product) {

            // 获取到期机具的二级商户信息
            if($userInfo == null || $userInfo['id'] != $product['user_id']) {
                $userInfo = $userService->getTopUserInfo($product['user_id']);
            }

            // 获取冻结机具所属代理商的冻结金额 检查是否和上次的机具类型一致
            if($productType == null || $productType['id'] != $product['type_id']){
                $productType = $productService->getAgentProductTypeInfo($product['agent_id'], $product['type_id']);
            }
            if(empty($userInfo) || empty($productType) || $productType['frozen_money'] <= 0){
                Yii::warning('冻结款，二级商户或机具类型不存在，userId:'.$userInfo['id'].'，typeId:'.$productType['id']);
                continue;
            }

            // 冻结二级代理商款项
            $agentFrozenLogService -> addFrozenLog($userInfo, $product, $productType['frozen_money']);

        }

    }


}