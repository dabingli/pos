<?php
namespace common\library\oss;

use Yii;
use yii\base\Component;
use OSS\OssClient;
use OSS\Croe\OssException;

/**
 * 阿里云oss
 *
 * @author zhouchen
 *        
 */
class Aliyunoss extends Component
{

    protected static $oss;

    protected $accessKeyId;

    protected $accessKeySecret;

    protected $endpoint;

    protected $bucket;

    public function init()
    {
        parent::init();
        $sysConfig = Yii::$app->debris->configAll();
        $this->accessKeyId = $sysConfig['storage_aliyun_accesskeyid']; // 获取阿里云oss的accessKeyId
        $this->accessKeySecret = $sysConfig['storage_aliyun_accesskeysecret']; // 获取阿里云oss的accessKeySecret
        $this->endpoint = 'http://' . $sysConfig['storage_aliyun_endpoint']; // 获取阿里云oss的endPoint
        $this->bucket = $sysConfig['storage_aliyun_bucket']; // 获取阿里云oss的endPoint
        self::$oss = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint); // 实例化OssClient对象
    }

    /**
     * 使用阿里云oss上传文件
     *
     * @param $object 保存到阿里云oss的文件名            
     * @param $filepath 文件在本地的绝对路径            
     * @return bool 上传是否成功
     */
    public function upload($object, $filepath)
    {
        if (self::$oss->uploadFile($this->bucket, $object, $filepath)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除指定文件
     *
     * @param $object 被删除的文件名            
     * @return bool 删除是否成功
     */
    public function delete($object)
    {
        if (self::$oss->deleteObject($this->bucket, $object)) {
            // 调用deleteObject方法把服务器文件上传到阿里云oss
            return true;
        } else {
            return false;
        }
    }
}