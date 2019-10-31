<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11
 * Time: 17:46
 */

namespace common\library\uni_push\uni_push\igetui;

class SimpleAlertMsg implements ApnMsg{
    public $alertMsg;

    public function get_alertMsg() {
        return $this->alertMsg;
    }
}