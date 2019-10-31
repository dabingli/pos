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

class InnerFiled_Type extends PBEnum
{
    const str  = 0;
    const int32  = 1;
    const int64  = 2;
    const floa  = 3;
    const doub  = 4;
    const bool  = 5;
}
