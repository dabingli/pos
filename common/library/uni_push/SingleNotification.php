<?php
namespace common\library\uni_push;

use Yii;
use common\library\uni_push\Base;
use common\library\uni_push\uni_push\IGeTui;
use common\library\uni_push\uni_push\igetui\IGtSingleMessage;
use common\library\uni_push\uni_push\igetui\IGtTarget;
use common\library\uni_push\uni_push\exception\RequestException;
use common\library\uni_push\uni_push\igetui\template\IGtNotificationTemplate;
use common\library\uni_push\uni_push\igetui\template\IGtNotyPopLoadTemplate;
use common\library\uni_push\uni_push\igetui\template\IGtTransmissionTemplate;

/**
 * 群发消息
 *
 * @author Administrator
 *        
 */
class SingleNotification extends Base
{
    // 发送host的uri
    private static $uri = '/apiex.htm';

    // 离线时间[微秒] 默认72小时
    public $time = 72 * 3600 * 1000;

    // 通知栏标题
    public $title = '';

    // 通知栏内容
    public $content = '';

    // 透传消息类型
    public $transmissionType = false;

    // 透传内容
    public $payload = [];

    // 通知栏logo
    public $logo = '';

    // 通知栏logo链接
    public $logoUrl = '';

    // 是否响铃
    public $isRing = true;

    // 是否震动
    public $isVibrate = true;

    // 通知栏是否可清除
    public $isClearable = true;

    // 客户端id
    public $clientId;

    public function rules()
    {
        return [
            [
                [
                    'time',
                    'logo',
                    'logoUrl',
                    'isRing',
                    'isVibrate',
                    'isClearable',
                    'payload'
                ],
                'safe'
            ],
            [
                [
                    'clientId',
                    'title',
                    'content'
                ],
                'required',
                'message' => '{attribute}不能为空'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => '通知标题',
            'content' => '通知内容',
            'time' => '离线时间',
            'logo' => 'logo',
            'logoUrl' => 'logo链接',
            'isRing' => '是否响铃',
            'isVibrate' => '是否震动',
            'isClearable' => '通知栏是否可清除',
            'transmissionType' => '透传消息类型', // false不传 true发送透传消息
            'payload' => '透传消息内容', // 字符串或数组数据
        ];
    }

    /**
     * @send 发送通知
     * @return mixed|null
     */
    public function send()
    {
        $igt = new IGeTui(self::getUrl(),$this->appKey, $this->masterSecret);
        //定义透传模板，设置透传内容，和收到消息是否立即启动启用
        $template = $this->getTransmissionTemplate();

        //定义"SingleMessage"
        $message = new IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime($this->time);//离线时间
        $message->set_data($template);

        //接收方
        $target = new IGtTarget();
        $target->set_appId($this->appId);
        $target->set_clientId($this->clientId);

        try {
            return $igt->pushMessageToSingle($message, $target);

        }catch(RequestException $e){
            $requstId =$e.getRequestId();
            //失败时重发
            return $igt->pushMessageToSingle($message, $target,$requstId);
        }
    }

    /**
     * @getTemplate 个推通知模板
     * @return IGtNotyPopLoadTemplate
     */
    public function getSingleTemplate(){
        $template =  new IGtNotyPopLoadTemplate();
        $template->set_appId($this->appId);                   //应用appid
        $template->set_appkey($this->appKey);                 //应用appkey

        /*if($this->transmissionType){
            $template->set_transmissionType(1);            //透传消息类型
            $template->set_payload($this->payload);//透传内容
        }*/

        $template ->set_notyTitle($this->title);                 //通知栏标题
        $template ->set_notyContent($this->content); //通知栏内容
        $template ->set_notyIcon($this->logo);                      //通知栏logo
        $template ->set_logoURL($this->logoUrl);                    //通知栏logo链接
        $template ->set_isBelled($this->isRing);                    //是否响铃
        $template ->set_isVibrationed($this->isVibrate);               //是否震动
        $template ->set_isCleared($this->isClearable);                   //通知栏是否可清除

        return $template;
    }

    /**
     * @getTemplate 群发通知模板
     * @return IGtNotificationTemplate
     */
    public function getTemplate(){
        $template =  new IGtNotificationTemplate();
        $template->set_appId($this->appId);                   //应用appid
        $template->set_appkey($this->appKey);                 //应用appkey

        if($this->transmissionType){
            $template->set_transmissionType(1);            //透传消息类型
            $template->set_transmissionContent($this->payload);//透传内容
        }

        $template->set_title($this->title);      //通知栏标题
        $template->set_text($this->content);     //通知栏内容
        $template->set_logo($this->logo);                       //通知栏logo
        $template->set_logoURL($this->logoUrl);                    //通知栏logo链接
        $template->set_isRing($this->isRing);                   //是否响铃
        $template->set_isVibrate($this->isVibrate);                //是否震动
        $template->set_isClearable($this->isClearable);              //通知栏是否可清除

        return $template;
    }

    /**
     * @getTransmissionTemplate 透传消息模板
     * @return IGtTransmissionTemplate
     */
    function getTransmissionTemplate(){
        $template =  new IGtTransmissionTemplate();
        //应用appid
        $template->set_appId($this->appId);
        //应用appkey
        $template->set_appkey($this->appKey);
        //透传消息类型
        $template->set_transmissionType(1);
        //透传内容
        $content = [
            'title' => $this->title,
            'content' => $this->content,
            'payload' => $this->payload,
        ];
        $template->set_transmissionContent(json_encode($content));
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //这是老方法，新方法参见iOS模板说明(PHP)*/
        $this->payload = is_array($this->payload) ? json_encode($this->payload) : $this->payload;
        $template->set_pushInfo("","",$this->content,
            "", $this->payload,"","","");
        return $template;
    }

    /**
     * 请求地址
     *
     * @return string
     */
    static public function getUrl()
    {
        return parent::getBaseUrl() . self::$uri;
    }
}