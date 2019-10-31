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

class ReqServListResult extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);
        $this->fields["1"] = PBMessage::NAMESPACE . '\protobuf\type\PBInt';
        $this->values["1"] = "";
        $this->fields["2"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["2"] = array();
        $this->fields["3"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["3"] = "";
    }
    function code()
    {
        return $this->_get_value("1");
    }
    function set_code($value)
    {
        return $this->_set_value("1", $value);
    }
    function host($offset)
    {
        $v = $this->_get_arr_value("2", $offset);
        return $v->get_value();
    }
    function append_host($value)
    {
        $v = $this->_add_arr_value("2");
        $v->set_value($value);
    }
    function set_host($index, $value)
    {
        $v = new $this->fields["2"]();
        $v->set_value($value);
        $this->_set_arr_value("2", $index, $v);
    }
    function remove_last_host()
    {
        $this->_remove_last_arr_value("2");
    }
    function host_size()
    {
        return $this->_get_arr_size("2");
    }
    function seqId()
    {
        return $this->_get_value("3");
    }
    function set_seqId($value)
    {
        return $this->_set_value("3", $value);
    }
}
