<?php
namespace common\components\crontabs;

use yii;

/**
 * 一个任务(crontab)的实例本身
 * Class CrontabJob
 * $commond = '* * * * * sleep 48; cd /var/www/html/mk-hyk; php yii crontab/notification >/dev/null 2>&1 & #notification-48';
 * 调用createFromCrontabLine($commond)之后的结果：
 * [
 * [enabled] => 1
 * [minutes] => *
 * [hours] => *
 * [dayOfMonth] => *
 * [months] => *
 * dayOfWeek] => *
 * [taskCommandLine] => sleep 48; cd /var/www/html/qiaodangjia; php yii crontab/notification >/dev/null 2>&1 &
 * [comments] => notification-48
 * [shortCut] =>
 * ]
 *
 * @package common\components\crontabs
 */
class CrontabJob
{

    /**
     * 任务是否启用
     * 禁用的状态会在命令前面加 # 符号
     *
     * @var boolean
     */
    public $enabled = true;

    /**
     * 分钟 (0 - 59)
     *
     * @var string/int
     */
    public $minutes;

    /**
     * 小时 (0 - 23)
     *
     * @var string/int
     */
    public $hours;

    /**
     * 天 (1 - 31)
     *
     * @var string/int
     */
    public $dayOfMonth;

    /**
     * 月 (1 - 12)
     *
     * @var string/int
     */
    public $months;

    /**
     * 周一到周日,0-6，或者使用名字
     *
     * @var tring/int
     */
    public $dayOfWeek;

    /**
     * 要被执行的任务命令行
     *
     * @var string
     */
    public $taskCommandLine;

    /**
     * 可选注释，会被放在任务行的最后#符号后面
     *
     * @var string
     */
    public $comments;

    /**
     *
     * @var string
     */
    public $shortCut;

    /**
     * 通过任务命令行创建CrontabJob对象的工厂方法
     *
     * @param string $crontabLine            
     * @throws InvalidArgumentException
     * @return CrontabJob
     */
    public static function createFromCrontabLine($crontabLine)
    {
        // 检查任务命令行是否正确有效
        $crontabLineRegex = '/^[\s\t]*(#)?[\s\t]*(([*0-9,-\/]+)[\s\t]+([*0-9,-\/]+)' . '[\s\t]+([*0-9,-\/]+)[\s\t]+([*a-z0-9,-\/]+)[\s\t]+([*a-z0-9,-\/]+)|' . '(@(reboot|yearly|annually|monthly|weekly|daily|midnight|hourly)))' . '[\s\t]+([^#]+)([\s\t]+#(.+))?$/';
        
        if (! preg_match($crontabLineRegex, $crontabLine, $matches)) {
            throw new \InvalidArgumentException('Crontab line not well formated then can\'t be parsed');
        }
        
        // 通过解析任务命令行来创建CrontabJob对象实例
        $crontabJob = new self();
        
        if (! empty($matches[1])) {
            $crontabJob->enabled = false;
        }
        
        if (! empty($matches)) {
            $crontabJob->minutes = $matches[3];
            $crontabJob->hours = $matches[4];
            $crontabJob->dayOfMonth = $matches[5];
            $crontabJob->months = $matches[6];
            $crontabJob->dayOfWeek = $matches[7];
        }
        
        if (! empty($matches[8])) {
            $crontabJob->shortCut = $matches[9];
        }
        
        $crontabJob->taskCommandLine = $matches[10];
        if (! empty($matches[12])) {
            $crontabJob->comments = $matches[12];
        }
        
        return $crontabJob;
    }

    /**
     * 把一个CrontabJob对象实例格式化为一个可供Linux系统的任务命令行
     *
     * @throws InvalidArgumentException
     * @return String
     */
    public function formatCrontabLine()
    {
        // 检查当前CrontabJob是否拥有任务命令行
        if (! isset($this->taskCommandLine) || empty($this->taskCommandLine)) {
            throw new \InvalidArgumentException('CrontabJob contain\'s no task command line');
        }
        
        $taskPlanningNotation = (isset($this->shortCut) && ! empty($this->shortCut)) ? sprintf('@%s', $this->shortCut) : sprintf('%s %s %s %s %s', (isset($this->minutes) ? $this->minutes : '*'), (isset($this->hours) ? $this->hours : '*'), (isset($this->dayOfMonth) ? $this->dayOfMonth : '*'), (isset($this->months) ? $this->months : '*'), (isset($this->dayOfWeek) ? $this->dayOfWeek : '*'));
        
        return sprintf('%s%s %s%s', ($this->enabled ? '' : '#'), $taskPlanningNotation, $this->taskCommandLine, (isset($this->comments) ? (' #' . $this->comments) : ''));
    }
}
