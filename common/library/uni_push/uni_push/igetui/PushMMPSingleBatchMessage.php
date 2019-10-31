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

class PushMMPSingleBatchMessage extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);
        $this->fields["1"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["1"] = "";
        $this->fields["2"] = PBMessage::NAMESPACE . '\igetui\PushMMPSingleMessage';
        $this->values["2"] = array();
        $this->fields["3"] = PBMessage::NAMESPACE . '\protobuf\type\PBBool';
        $this->values["3"] = "";
        $this->values["3"] = new PBBool();
        $this->values["3"]->value = true;
    }
    function batchId()
    {
        return $this->_get_value("1");
    }
    function set_batchId($value)
    {
        return $this->_set_value("1", $value);
    }
    function batchItem($offset)
    {
        return $this->_get_arr_value("2", $offset);
    }
    function add_batchItem()
    {
        return $this->_add_arr_value("2");
    }
    function set_batchItem($index, $value)
    {
        $this->_set_arr_value("2", $index, $value);
    }
    function remove_last_batchItem()
    {
        $this->_remove_last_arr_value("2");
    }
    function batchItem_size()
    {
        return $this->_get_arr_size("2");
    }
    function isSync()
    {
        return $this->_get_value("3");
    }
    function set_isSync($value)
    {
        return $this->_set_value("3", $value);
    }
}
