<?php
/**
 * 个推IOS多媒体消息，支持图片、音频、视频
 * Created by PhpStorm.
 * User: zqzhao5
 * Date: 17-7-27
 * Time: 下午3:21
 */

namespace common\library\uni_push\uni_push\igetui;

class IGtMultiMedia {
    /**
     * @public资源ID
     */
    public $rid;
    /**
     * @public资源url
     */
    public $url;
    /**
     * @public资源类型
     */
    public $type;
    /**
     * @public是否只支持wifi下发
     */
    public $onlywifi = 0;

    public function __construct(){}

    function get_rid() {
        return $this->rid;
    }

    function  set_rid($rid) {
        $this->rid = $rid;
        return $this;
    }

    function get_url() {
        return $this->url;
    }

    function set_url($url) {
        $this->url = $url;
        return$this;
    }

    function get_type() {
        return $this -> type;
    }

    function set_type($type) {
        $this -> type = $type;
        return $this;
    }

    function set_onlywifi($onlywifi) {
        $this -> onlywifi = $onlywifi ? 1:0;
        return $this;
    }

    function get_onlywifi() {
        return $this -> onlywifi;
    }
}
