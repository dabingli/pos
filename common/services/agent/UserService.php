<?php
namespace common\services\agent;

use common\services\Service;
use common\models\user\User;

class UserService extends Service
{

    /**
     * @getTopUserInfo 获取顶级或二级推荐商户信息
     * @param null $userId
     * @param bool $Top [true 1级  false 2级]
     * @return array
     */
    public function getTopUserInfo($userId=null, $Top=false)
    {
        static $topData = [];
        static $subData = [];

        $userModel = new User();
        $user = $userModel->findOne($userId);

        if(!empty($user)){

            if(empty($user['parent_id'])){
                $topData = $user;
            }else{
                $subData = $user;
                $this->getTopUserInfo($user['parent_id'], $Top);
            }

        }

        return $Top ? $topData : $subData;
    }

}