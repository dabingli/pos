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

class PushListMessage extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);
        $this->fields["1"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["1"] = "";
        $this->fields["2"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["2"] = "";
        $this->fields["3"] = PBMessage::NAMESPACE . '\igetui\Target';
        $this->values["3"] = array();
    }
    function seqId()
    {
        return $this->_get_value("1");
    }
    function set_seqId($value)
    {
        return $this->_set_value("1", $value);
    }
    function taskId()
    {
        return $this->_get_value("2");
    }
    function set_taskId($value)
    {
        return $this->_set_value("2", $value);
    }
    function targets($offset)
    {
        return $this->_get_arr_value("3", $offset);
    }
    function add_targets()
    {
        return $this->_add_arr_value("3");
    }
    function set_targets($index, $value)
    {
        $this->_set_arr_value("3", $index, $value);
    }
    function remove_last_targets()
    {
        $this->_remove_last_arr_value("3");
    }
    function targets_size()
    {
        return $this->_get_arr_size("3");
    }
}