<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11
 * Time: 17:54
 */

namespace common\library\uni_push\uni_push\igetui;

use common\library\uni_push\uni_push\protobuf\PBMessage;
use common\library\uni_push\uni_push\protobuf\type\PBEnum;
use common\library\uni_push\uni_push\protobuf\type\PBBool;

class ReqServListResult_ReqServHostResultCode extends PBEnum
{
    const successed  = 0;
    const failed  = 1;
    const busy  = 2;
}
