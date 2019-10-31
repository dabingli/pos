<?php
namespace common\components\crontabs;

use yii;

/**
 * Class CrontabAdapter
 *
 * @package common\components\crontabs
 */
class CrontabAdapter
{

    private $userName;

    private $useSudo;

    /**
     * 初始化一个任务命令适配器
     *
     * @param string $userName
     *            可选，读取哪个用户的任务 (默认用户 = 当前用户)
     * @param boolean $useSudo
     *            是否使用sudo命令
     *            
     *            关于 sudo ：
     *            如果你想要让当前用户执行某个用户的任务(crontab)
     *            可以参考一下例子:
     *            用户(user) www-data 想要编辑 waylon 任务(crontab):
     *            
     *            www-data ALL=(waylon) NOPASSWD: /usr/bin/crontab
     *            
     *            将会告诉 sudo 用户(www-data )在不需要输入密码的情况下，
     *            可以代替用户(waylon) 执行 /usr/bin/crontab 任务
     */
    public function __construct($userName = null, $useSudo = false)
    {
        if ($userName) {
            $this->userName = $userName;
        }
        $this->useSudo = $useSudo;
    }

    /**
     * 读取并返回一个定时任务的原数据(命令行)
     *
     * @return string $output 任务原数据(命令行)
     */
    public function readCrontab()
    {
        $crontabCommandLine = (isset($this->userName) && $this->useSudo) ? sprintf('sudo -n -u %s crontab -l', $this->userName) : ($this->userName ? sprintf('crontab -u %s -l', $this->userName) : 'crontab -l');
        
        exec($crontabCommandLine . ' 2>&1', $output, $exitCode);
        
        /* exec 错误处理 */
        if ($exitCode !== 0) {
            /* 特殊情况 : 正常读取任务的时候，任务是空的，抛出异常的退出代码(exit code) */
            if (! preg_match('/^no crontab for .+$/', $output[0])) {
                throw new \DomainException('Error when trying to read crontab : ' . implode(' ', $output));
            } else {
                $output = '';
            }
        } else {
            $output = implode("\n", $output);
        }
        
        return $output;
    }

    /**
     * 把任务数据写到任务中
     *
     * @param string $crontabRawData            
     */
    public function writeCrontab($crontabRawData)
    {
        $crontabRawData = escapeshellarg($crontabRawData);
        
        $crontabCommandLine = (isset($this->userName) && $this->useSudo) ? sprintf('echo %s | sudo -n -u %s crontab -', $crontabRawData, $this->userName) : ($this->userName ? sprintf('echo %s | crontab -u %s -', $crontabRawData, $this->userName) : sprintf('echo %s | crontab -', $crontabRawData));
        
        exec($crontabCommandLine . ' 2>&1', $output, $exitCode);
        
        /* exec 错误处理 */
        if ($exitCode !== 0) {
            throw new \DomainException('Error when trying to write crontab : ' . implode(' ', $output));
        }
    }
}
