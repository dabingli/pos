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

class CmdID extends PBEnum
{
    const GTHEARDBT  = 0;
    const GTAUTH  = 1;
    const GTAUTH_RESULT  = 2;
    const REQSERVHOST  = 3;
    const REQSERVHOSTRESULT  = 4;
    const PUSHRESULT  = 5;
    const PUSHOSSINGLEMESSAGE  = 6;
    const PUSHMMPSINGLEMESSAGE  = 7;
    const STARTMMPBATCHTASK  = 8;
    const STARTOSBATCHTASK  = 9;
    const PUSHLISTMESSAGE  = 10;
    const ENDBATCHTASK  = 11;
    const PUSHMMPAPPMESSAGE  = 12;
    const SERVERNOTIFY  = 13;
    const PUSHLISTRESULT  = 14;
    const SERVERNOTIFYRESULT  = 15;
    const STOPBATCHTASK  = 16;
    const STOPBATCHTASKRESULT  = 17;
    const PUSHMMPSINGLEBATCH  = 18;
}
