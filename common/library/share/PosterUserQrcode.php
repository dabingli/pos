<?php
namespace common\library\share;

use Yii;

/**
 * $posterUserQrcode = new PosterUserQrcode();
 * $posterTargetImage='E:/phpStudy/PHPTutorial/WWW/test/poster.jpg';
 * $posterBg='190225.png';
 * $posterQrCode='f045171396c6ff48e81b21e91b1bc5fd-15-35.png';
 * $posterUserQrcode->synthesis($posterTargetImage, $posterBg, $posterQrCode);
 * zhouchen
 *
 * 个人海报图片合成
 *
 * @author Administrator
 *        
 */
class PosterUserQrcode
{

    /**
     * 二维码X轴
     *
     * @var unknown
     */
    public $qrcodeX = 369;

    /**
     * 二维码Y轴
     *
     * @var unknown
     */
    public $qrcodeY = 191;

    /**
     * 二维码缩至宽度
     *
     * @var unknown
     */
    public $qrcodeSizeWidth = 307;

    /**
     * 二维码缩至高度
     *
     * @var unknown
     */
    public $qrcodeSizeHeight = 307;

    private $_posterBackground;

    private $_posterWidth;

    private $_posterHeight;

    private $_target;

    private $_white;

    private $_posterQrcode;

    private $_resizeImageQrcode;

    public $posterBg;

    public $userName;

    public $posterQrCode;

    /**
     * 根据图片类型选择恰当的图片资源读取函数
     *
     * @param unknown $imgPath            
     * @return resource|NULL
     */
    public function imageCreateFromPath($imgPath)
    {
        list ($width, $height, $type, $attr) = getimagesize($imgPath);
        switch ($type) {
            case 3: // png
                return imagecreatefrompng($imgPath);
            case 2: // jpeg
                return imagecreatefromjpeg($imgPath);
            default:
                return null;
        }
    }

    /**
     * 背景处理
     *
     * @param unknown $posterBg            
     * @throws \Exception
     */
    protected function posterBg()
    {
        // 海报主背景
        // @todo $cachePosterBg 确保图片已经从oss下载到本地再执行图片合成操作
        $this->_posterBackground = $this->imageCreateFromPath($this->posterBg);
        if (empty($this->_posterBackground)) {
            throw new \Exception("背景资源读取失败");
        }
        $this->_posterWidth = imagesx($this->_posterBackground);
        $this->_posterHeight = imagesy($this->_posterBackground);
        // 目标填充对象
        $this->_target = imagecreatetruecolor($this->_posterWidth, $this->_posterHeight);
        $this->_white = imagecolorallocate($this->_target, 255, 255, 255);
        imagefill($this->_target, 0, 0, $this->_white);
        imagecopyresampled($this->_target, $this->_posterBackground, 0, 0, 0, 0, $this->_posterWidth, $this->_posterHeight, $this->_posterWidth, $this->_posterHeight);
    }

    protected function posterQrCode()
    {
        // 合成二维码图片
        $this->_posterQrcode = $this->imageCreateFromPath($this->posterQrCode);
        if (empty($this->_posterQrcode)) {
            throw new \Exception("个人二维码资源读取失败");
        }
        $this->_resizeImageQrcode = $this->resizeImage($this->_posterQrcode, $this->qrcodeSizeWidth, $this->qrcodeSizeHeight);
        imagedestroy($this->_posterQrcode);
        $codeImageWidth = imagesx($this->_resizeImageQrcode);
        $codeImageHeight = imagesy($this->_resizeImageQrcode);
        imagecopymerge($this->_target, $this->_resizeImageQrcode, $this->qrcodeX, $this->qrcodeY, 0, 0, $this->qrcodeSizeWidth, $this->qrcodeSizeHeight, 100);
    }

    /**
     * 开始合成
     *
     * @param unknown $posterTargetImage合成后保存的图片地址名称            
     * @param unknown $posterBg
     *            合成的背景
     * @param unknown $posterQrCode
     *            合成的个人二维码
     * @return unknown
     */
    public function synthesis($posterTargetImage, $posterBg, $posterQrCode)
    {
        $this->posterBg = $posterBg;
        
        $this->posterQrCode = $posterQrCode;
        
        $this->posterBg();
        
        $this->posterQrCode();
        // 生成目标图片(注意: $quality 不能太高, 避免图片太大超时)
        imagejpeg($this->_target, $posterTargetImage, 90);
        
        // 释放资源
        imagedestroy($this->_posterBackground);
        imagedestroy($this->_target);
        imagedestroy($this->_resizeImageQrcode);
        unset($this->_posterBackground);
        unset($this->_posterWidth);
        unset($this->_posterHeight);
        unset($this->_target);
        unset($this->_white);
        unset($this->_posterQrcode);
        unset($this->_resizeImageQrcode);
        
        return $posterTargetImage;
    }

    /**
     * 图像等比缩放处理函数
     *
     * @param unknown $im            
     * @param unknown $scaleWidth            
     * @param unknown $scaleHeight            
     * @param string $savePath            
     */
    protected function resizeImage($im, $scaleWidth, $scaleHeight, $savePath = '')
    {
        if (! $im) {
            return null;
        }
        
        $pic_width = imagesx($im);
        $pic_height = imagesy($im);
        
        // 尺寸不合法, 直接返回原图
        if ($scaleWidth <= 0 || $scaleHeight <= 0) {
            return $im;
        }
        
        // 计算缩放比例
        $widthRatio = $scaleWidth / $pic_width;
        $heightRatio = $scaleHeight / $pic_height;
        $scaleRatio = ($widthRatio < $heightRatio) ? $widthRatio : $heightRatio;
        
        // 计算新图像宽高
        $newWidth = $pic_width * $scaleRatio;
        $newHeight = $pic_height * $scaleRatio;
        
        // 缩放处理
        if (function_exists("imagecopyresampled")) {
            $newim = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newim, $im, 0, 0, 0, 0, $newWidth, $newHeight, $pic_width, $pic_height);
        } else {
            $newim = imagecreate($newWidth, $newHeight);
            imagecopyresized($newim, $im, 0, 0, 0, 0, $newWidth, $newHeight, $pic_width, $pic_height);
        }
        
        // 如果传入了保存路径, 则将缩放后的图片写入保存
        if (! empty($savePath)) {
            imagejpeg($newim, $savePath);
            imagedestroy($newim);
        } else { // 不写入文件直接使用, 记得使用完释放掉
            return $newim;
        }
    }
}