<?php
namespace common\library\share;

use Yii;

/**
 * 海报的密码处理
 *
 * @author Administrator
 *        
 */
class Password
{

    const LONG_LIMIT = 200;

    protected $secretKey;

    protected $domain;

    public $encryptName = 'share';

    public function __construct()
    {
        $this->secretKey = json_encode([
            Yii::$app->params['share']['secretKey'],
            get_called_class()
        ]);
        $this->domain = Yii::$app->params['share']['domain'];
    }

    /**
     * 加密
     *
     * @param unknown $data            
     * @return string
     */
    public function encrypt($data)
    {
        // 无论是什么数值类型，先给json一下
        $json = json_encode($data);
        $encryptedData = base64_encode(Yii::$app->getSecurity()->encryptByPassword($json, $this->secretKey));
        return $encryptedData;
    }

    /**
     * 超过245字符字符串加不了密，就得将字符串每次分200个去加密
     *
     * @param unknown $data            
     * @return string
     */
    public function longEncrypt($data)
    {
        $arr = [];
        // 无论是什么数值类型，先给json一下
        $data = json_encode($data);
        $strlen = strlen($data); // 字符串总长度
        $count = intval($strlen / self::LONG_LIMIT); // 循环次数
        for ($i = 0; $i <= $count; $i ++) {
            $offset = $i * self::LONG_LIMIT;
            $arr[] = $this->encrypt(substr($data, $offset, self::LONG_LIMIT));
        }
        return json_encode($arr);
    }

    public function getEncryptUrl($data)
    {
        return $this->domain . '/share/index/signup.html?' . $this->encryptName . '=' . urlencode($this->longEncrypt($data));
    }

    public function getUrlData()
    {
        return Yii::$app->request->get($this->encryptName);
    }

    /**
     * 解密
     *
     * @param unknown $data            
     * @return string
     */
    public function decrypt($encryptedData)
    {
        return json_decode(Yii::$app->getSecurity()->decryptByPassword(base64_decode($encryptedData), $this->secretKey), true);
    }

    /**
     * 长字符串解密
     *
     * @param unknown $json            
     * @return boolean|string
     */
    public function longDecrypt($json)
    {
        if (empty($json)) {
            return false;
        }
        $strl = '';
        $arr = json_decode($json, true);
        if (empty($arr)) {
            return $strl = $this->decrypt($arr);
        } else {
            foreach ($arr as $k => $val) {
                $strl .= $this->decrypt($val);
            }
        }
        return json_decode($strl, true);
    }
}