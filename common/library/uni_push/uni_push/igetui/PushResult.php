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

class PushResult extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);
        $this->fields["1"] = PBMessage::NAMESPACE . '\igetui\PushResult_EPushResult';
        $this->values["1"] = "";
        $this->fields["2"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["2"] = "";
        $this->fields["3"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["3"] = "";
        $this->fields["4"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["4"] = "";
        $this->fields["5"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["5"] = "";
        $this->fields["6"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["6"] = "";
        $this->fields["7"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["7"] = "";
        $this->fields["8"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["8"] = "";
    }
    function result()
    {
        return $this->_get_value("1");
    }
    function set_result($value)
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
    function messageId()
    {
        return $this->_get_value("3");
    }
    function set_messageId($value)
    {
        return $this->_set_value("3", $value);
    }
    function seqId()
    {
        return $this->_get_value("4");
    }
    function set_seqId($value)
    {
        return $this->_set_value("4", $value);
    }
    function target()
    {
        return $this->_get_value("5");
    }
    function set_target($value)
    {
        return $this->_set_value("5", $value);
    }
    function info()
    {
        return $this->_get_value("6");
    }
    function set_info($value)
    {
        return $this->_set_value("6", $value);
    }
    function traceId()
    {
        return $this->_get_value("7");
    }
    function set_traceId($value)
    {
        return $this->_set_value("7", $value);
    }
    function batchId()
    {
        return $this->_get_value("8");
    }
    function set_batchId($value)
    {
        return $this->_set_value("8", $value);
    }
}
