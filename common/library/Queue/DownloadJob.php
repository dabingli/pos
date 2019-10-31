<?php
namespace Queue;

use yii;
use Queue\BaseObject;

class DownloadJob extends BaseObject
{

    public $url;

    public $file;

    public function execute($queue)
    {
        file_put_contents($this->file, $this->url);
        //return 1111;
    }
}