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

class PushListResult extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);
        $this->fields["1"] = PBMessage::NAMESPACE . '\igetui\PushResult';
        $this->values["1"] = array();
    }
    function results($offset)
    {
        return $this->_get_arr_value("1", $offset);
    }
    function add_results()
    {
        return $this->_add_arr_value("1");
    }
    function set_results($index, $value)
    {
        $this->_set_arr_value("1", $index, $value);
    }
    function remove_last_results()
    {
        $this->_remove_last_arr_value("1");
    }
    function results_size()
    {
        return $this->_get_arr_size("1");
    }
}
