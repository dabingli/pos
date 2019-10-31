<?php

namespace common\library\uni_push\uni_push\igetui;

class IGtTagMessage extends IGtMessage{

    //array('','',..)
    public $appIdList;
    public $tag;
    public $speed=0;

    function __construct(){
        parent::__construct();
    }

    function get_appIdList() {
        return $this->appIdList;
    }

    function  set_appIdList($appIdList) {
        $this->appIdList = $appIdList;
    }

    function get_tag() {
        return $this->tag;
    }

    function set_tag($tag) {
        $this->tag = $tag;
    }

    function get_speed()
    {
        return $this->speed;
    }
    function set_speed($speed)
    {
        $this->speed=$speed;
    }
}