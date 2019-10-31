<?php
namespace common\helpers;

class Sensitive
{

    static public function mobile($mobile)
    {
        return str_replace(mb_substr($mobile, 3, 4, 'utf-8'), '****', $mobile);
    }

    static public function userName($userName)
    {
        return str_replace(mb_substr($userName, 1, 1, 'utf-8'), '*', $userName);
    }
}
