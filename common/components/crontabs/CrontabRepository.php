<?php
namespace common\components\crontabs;

use yii;

/**
 * Class CrontabRepository
 * 任务仓库
 * Access and manage CrontabJob Objects (add, modify, delete).
 * 访问和管理CrontabJob对象(添加，修改，删除)
 *
 * @package common\components\crontabs
 */
class CrontabRepository
{

    private $crontabAdapter;

    private $crontabJobs = [];

    /**
     * 任务文件顶部的注释
     *
     * @var string
     */
    public $headerComments;

    /**
     * 解析定时任务的时候，排除不是任务命令
     *
     * @var array
     */
    public $crontabLinesToBypass = [
        // 用来代表解析默认的Ubuntu任务头部例子
        '# 0 5 * * 1 tar -zcf /var/backups/home.tgz /home/'
    ];

    /**
     * 实例化一个CrontabRepository仓库实例
     * CrontabAdapter用来提供系统的"crontab"命令行
     *
     * @param CrontabAdapter $crontabAdapter            
     */
    public function __construct(CrontabAdapter $crontabAdapter)
    {
        $this->crontabAdapter = $crontabAdapter;
        $this->readCrontab();
    }

    /**
     * 返回所有定时任务实例
     *
     * @return array CrontabJobs数组
     */
    public function getJobs()
    {
        return $this->crontabJobs;
    }

    /**
     * 通过命令行特征来查找任务实例
     *
     * @param String $regex            
     * @throws InvalidArgumentException
     * @return array CrontabJobs数组
     */
    public function findJobByRegex($regex)
    {
        /* Test if regex is valid */
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \Exception($message);
        });
        
        try {
            preg_match($regex, 'test');
            restore_error_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            throw new \InvalidArgumentException('Not a valid Regex : ' . $e->getMessage());
            return;
        }
        
        $crontabJobs = [];
        
        if (! empty($this->crontabJobs)) {
            foreach ($this->crontabJobs as $crontabJob) {
                if (preg_match($regex, $crontabJob->formatCrontabLine())) {
                    array_push($crontabJobs, $crontabJob);
                }
            }
        }
        
        return $crontabJobs;
    }

    /**
     * 新增一个定时任务
     *
     * @param CrontabJob $crontabJob            
     */
    public function addJob(CrontabJob $crontabJob)
    {
        if (array_search($crontabJob, $this->crontabJobs) !== false) {
            $exceptionMessage = 'This job is already in the crontab. Please consider cloning the' . 'CrontabJob object if you want it to be registered twice.';
            throw new \LogicException($exceptionMessage);
        }
        array_push($this->crontabJobs, $crontabJob);
    }

    /**
     * 移除一个CrontabJob定时任务
     *
     * @param CrontabJob $crontabJob            
     */
    public function removeJob(CrontabJob $crontabJob)
    {
        $jobKey = array_search($crontabJob, $this->crontabJobs, true);
        if ($jobKey === false) {
            throw new \LogicException('This job is not part of this crontab');
        }
        unset($this->crontabJobs[$jobKey]);
    }

    /**
     * 保存所有活动任务的选项，并启动
     */
    public function persist()
    {
        $crontabRawData = '';
        if (! empty($this->headerComments)) {
            $crontabRawData .= $this->headerComments;
        }
        
        if (! empty($this->crontabJobs)) {
            foreach ($this->crontabJobs as $crontabJob) {
                try {
                    $crontabLine = $crontabJob->formatCrontabLine();
                    $crontabRawData .= ($crontabLine . "\n");
                } catch (\Exception $e) {
                    ;
                }
            }
        }
        
        $this->crontabAdapter->writeCrontab($crontabRawData);
    }

    /**
     * 读取系统的原命令行并解析它，
     * 解析好的命令会自动放入 $this->crontabJobs数组中
     */
    private function readCrontab()
    {
        // windows系统直接返回
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }
        
        $crontabRawData = $this->crontabAdapter->readCrontab();
        
        if (empty($crontabRawData)) {
            return;
        }
        
        $crontabRawLines = explode("\n", $crontabRawData);
        
        foreach ($crontabRawLines as $crontabRawLine) {
            
            try {
                // 使用任务工厂方法来测试当前命令行是否是定时任务命令
                $crontabJob = CrontabJob::createFromCrontabLine($crontabRawLine);
                $isCrontabJob = true;
            } catch (\Exception $e) {
                $isCrontabJob = false;
            }
            
            if ($isCrontabJob && ! in_array($crontabRawLine, $this->crontabLinesToBypass)) {
                array_push($this->crontabJobs, $crontabJob);
            } else {
                // 如果没有任何的crontabjobs，那么该行就是头部注释
                if (empty($this->crontabJobs)) {
                    if (empty($this->headerComments)) {
                        $this->headerComments = $crontabRawLine . "\n";
                    } else {
                        $this->headerComments .= ($crontabRawLine . "\n");
                    }
                }
            }
        }
    }
}
