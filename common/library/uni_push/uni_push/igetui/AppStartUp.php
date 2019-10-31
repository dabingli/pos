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

class AppStartUp extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);

        $this->fields["1"] = PBMessage::NAMESPACE .'\protobuf\type\PBString';
        $this->values["1"] = "";
        $this->fields["2"] = PBMessage::NAMESPACE .'\protobuf\type\PBString';
        $this->values["2"] = "";
        $this->fields["3"] = PBMessage::NAMESPACE .'\protobuf\type\PBString';
        $this->values["3"] = "";
    }
    function android()
    {
        return $this->_get_value("1");
    }
    function set_android($value)
    {
        return $this->_set_value("1", $value);
    }
    function symbia()
    {
        return $this->_get_value("2");
    }
    function set_symbia($value)
    {
        return $this->_set_value("2", $value);
    }
    function ios()
    {
        return $this->_get_value("3");
    }
    function set_ios($value)
    {
        return $this->_set_value("3", $value);
    }
}
