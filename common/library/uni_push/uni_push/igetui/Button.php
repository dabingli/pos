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

class Button extends PBMessage
{
    public $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
    public function __construct($reader=null)
    {
        parent::__construct($reader);

        $this->fields["1"] = PBMessage::NAMESPACE .'\protobuf\type\PBString';
        $this->values["1"] = "";
        $this->fields["2"] = PBMessage::NAMESPACE .'\protobuf\type\PBInt';
        $this->values["2"] = "";
    }
    function text()
    {
        return $this->_get_value("1");
    }
    function set_text($value)
    {
        return $this->_set_value("1", $value);
    }
    function next()
    {
        return $this->_get_value("2");
    }
    function set_next($value)
    {
        return $this->_set_value("2", $value);
    }
}
