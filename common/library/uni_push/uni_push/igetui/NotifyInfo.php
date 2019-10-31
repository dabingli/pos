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

class NotifyInfo extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);
        $this->fields["1"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["1"] = "";
        $this->fields["2"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["2"] = "";
        $this->fields["3"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["3"] = "";
        $this->fields["4"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["4"] = "";
        $this->fields["5"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["5"] = "";
        $this->fields["6"] = PBMessage::NAMESPACE . '\igetui\NotifyInfo_Type';
        $this->values["6"] = "";
        $this->values["6"] = new NotifyInfo_Type();
        $this->values["6"]->value = NotifyInfo_Type::_payload;
        $this->fields["7"] = PBMessage::NAMESPACE . '\protobuf\type\PBString';
        $this->values["7"] = "";
    }
    function title()
    {
        return $this->_get_value("1");
    }
    function set_title($value)
    {
        return $this->_set_value("1", $value);
    }
    function content()
    {
        return $this->_get_value("2");
    }
    function set_content($value)
    {
        return $this->_set_value("2", $value);
    }
    function payload()
    {
        return $this->_get_value("3");
    }
    function set_payload($value)
    {
        return $this->_set_value("3", $value);
    }
    function intent()
    {
        return $this->_get_value("4");
    }
    function set_intent($value)
    {
        return $this->_set_value("4", $value);
    }
    function url()
    {
        return $this->_get_value("5");
    }
    function set_url($value)
    {
        return $this->_set_value("5", $value);
    }
    function type()
    {
        return $this->_get_value("6");
    }
    function set_type($value)
    {
        return $this->_set_value("6", $value);
    }
    function notifyId()
    {
        return $this->_get_value("7");
    }
    function set_notifyId($value)
    {
        return $this->_set_value("7", $value);
    }
}
