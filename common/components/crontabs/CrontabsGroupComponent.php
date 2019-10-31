<?php
namespace common\components\crontabs;

use yii;
use yii\base\Component;
use yii\base\InvalidValueException;

/**
 * $repository = new CrontabRepository(new CrontabAdapter);
 * $crontabs = file(__DIR__.DIRECTORY_SEPARATOR.'crontabs.php');
 * foreach ($crontabs as $crontab){
 * $repository->addJob(CrontabJob::createFromCrontabLine($crontab));
 * }
 * $jobs = $repository->findJobByRegex('/(.s)*\#close-order/');
 * print_r($jobs);
 *
 * Class CrontabsGroupComponent
 *
 * @package common\components
 */
class CrontabsGroupComponent extends Component
{

    private $root = '/var/www/html';

    private $output = '/dev/null';

    //
    private $groupNameRegex = '(.s)*\#';

    // Crontab中有可能没有php的环境
    public $phpYiiCommand = 'php yii';

    private $cdCommand = 'cd';

    private $crontabRepository;

    public function init()
    {
        $this->root = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR;
        $this->crontabRepository = new CrontabRepository(new CrontabAdapter());
    }

    /**
     *
     * @param
     *            $groupName
     * @param
     *            $route
     * @param int $groupJobsCount
     *            一分钟内轮询多少条任务，最大60条
     * @return mixed
     */
    public function persistByGroupName($groupName, $route, $groupJobsCount = 5, $minutes = '*', $hours = '*', $dayOfMonth = '*', $months = '*', $dayOfWeek = '*')
    {
        $groupJobsCount = (int) $groupJobsCount;
        if ($groupJobsCount > 60) {
            throw new InvalidValueException('Max jobs count is 60,now is ' . $groupJobsCount);
        }
        
        // 清除该分组的任务
        $this->clearJobsByGroupName($groupName);
        
        $sleepInterval = 60 / $groupJobsCount;
        for ($i = 0; $i < $groupJobsCount; $i ++) {
            $crontabJob = new CrontabJob();
            $crontabJob->minutes = $minutes;
            $crontabJob->hours = $hours;
            $crontabJob->dayOfMonth = $dayOfMonth;
            $crontabJob->months = $months;
            $crontabJob->dayOfWeek = $dayOfWeek;
            if (0 == $i) {
                $crontabJob->taskCommandLine = $this->getJobCommandLine($groupName, $route);
            } else {
                $sleep = $sleepInterval * $i;
                $crontabJob->taskCommandLine = $this->getJobCroupCommandLine($sleep, $groupName, $route);
            }
            $crontabJob->comments = self::jobName($groupName, $i);
            $this->crontabRepository->addJob($crontabJob);
        }
        return $this->crontabRepository->persist();
    }

    /**
     *
     * @param
     *            $groupName
     */
    public function clearJobsByGroupName($groupName)
    {
        $jobs = $this->findJobsByGroupName($groupName);
        foreach ($jobs as $job) {
            $this->crontabRepository->removeJob($job);
        }
        return $this->crontabRepository->persist();
    }

    /**
     *
     * @param $groupName 按分组名称来查找任务            
     * @return array 某分组的CrontabJob数组
     */
    public function findJobsByGroupName($groupName)
    {
        return (array) $this->crontabRepository->findJobByRegex("/{$this->groupNameRegex}{$groupName}/");
    }

    /**
     *
     * @param $groupName 按分组名称来查找任务            
     * @return array 某分组的CrontabJob数组
     */
    public function countJobsByGroupName($groupName)
    {
        return count($this->findJobsByGroupName($groupName));
    }

    /**
     *
     * @param
     *            $groupName
     * @param
     *            $i
     * @return string
     */
    private static function jobName($groupName, $i)
    {
        return $groupName . '-' . $i;
    }

    /**
     * 启动进程的时候带上一个进程识别符
     * 返回类似格式：
     * cd /var/www/html/mk-hyk; php yii crontab/notification /var/www/html/mk-libs/wechat-notification >/dev/null 2>&1 &
     *
     * @param
     *            $sleep
     */
    private function getJobCommandLine($groupName, $route)
    {
        // 2>&1 是把错误输出导入（合并）到标准输出流中
        $processToken = $this->getProcessToken($groupName);
        return "{$this->cdCommand} {$this->root}; {$this->phpYiiCommand} {$route} {$processToken} >{$this->output} 2>&1 &";
    }

    /**
     * 进程识别符
     *
     * @param
     *            $groupName
     * @return string
     */
    private function getProcessToken($groupName)
    {
        return md5($this->root . $groupName);
    }

    /**
     * 返回类似格式：sleep 48; cd /var/www/html/mk-hyk/; php yii crontab/notification >/dev/null 2>&1 &
     *
     * @param
     *            $sleep
     * @param
     *            $route
     * @return string
     */
    private function getJobCroupCommandLine($sleep, $groupNamem, $route)
    {
        return "sleep {$sleep}; " . $this->getJobCommandLine($groupNamem, $route);
    }

    /**
     * 返回已经启动的PHP进程，通过进程唯一识别符(process token)来获知定时任务启动的监听进程
     * 注意：如果定时任务启动的不是监听程序，那么该PHP进程执行完之后就会自动销毁。
     *
     * @param $groupName 任务组名，用来识别业务            
     * @param $route console控制台对应的应用程序(application
     *            route)路由
     * @return int 进程数量
     */
    public function getJobsProcessCount($groupName, $route)
    {
        $jobProcessCommand = $this->getJobProcessCommand($groupName, $route);
        $cmd = popen("ps -ef | {$jobProcessCommand} | wc -l", 'r');
        $num = fread($cmd, 512);
        pclose($cmd);
        return (int) $num;
    }

    /**
     * ps -ef | grep 'php yii worker/listen 3a34facf9ddaad970db9f13910161a4d ' | grep -v grep | grep -v '#wechat-notification' | pgrep php
     * 移除(kill)掉所有对应($groupName, $route)定时任务开启的进程
     *
     * @param
     *            $groupName
     * @param
     *            $route
     */
    public function removeJobsProcessByGroupName($groupName, $route)
    {
        $jobProcessCommand = $this->getJobProcessCommand($groupName, $route);
        // $cmd = popen("ps -ef | {$jobProcessCommand} | pgrep php | xargs kill -s 9", 'w');//2018-6-6修改，这个命令会把nginx下的php-fpm给一起清了
        // echo "ps -ef | {$jobProcessCommand} | awk '{print $2}' | xargs kill -s 9";die;
        $cmd = popen("ps -ef | {$jobProcessCommand} | awk '{print $2}' | xargs kill -s 9", 'w');
        pclose($cmd);
    }

    /**
     * 进程命令：根据($groupName, $route)匹配出对应的进程
     *
     * @param
     *            $groupName
     * @param
     *            $route
     * @return string
     */
    private function getJobProcessCommand($groupName, $route)
    {
        $processToken = $this->getProcessToken($groupName);
        return "grep '{$this->phpYiiCommand} {$route} {$processToken}' | grep -v grep | grep -v '#{$groupName}'";
    }
}