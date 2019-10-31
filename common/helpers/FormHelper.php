<?php

namespace common\helpers;

/**
 * 表单辅助工具
 */
class FormHelper
{

    /*
     * 把 $model->errors 转为字符串
     * @params $errors
     * @return string
     */
    static public function multiErrors2Msg(array $errors)
    {
        $arr = [];
        
        foreach ($errors as $error) {
            $arr[] = $error[0];
        }
        
        if (! empty($arr)) {
            return implode("<br/>", $arr);
        }
        
        return '';
    }
}