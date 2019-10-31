<?php 
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = '注册';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width">

    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title><?php echo \Yii::$app->params['title'] ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo \yii::$app->request->baseUrl; ?>/css/all.css">
    <link rel="stylesheet" type="text/css" href="<?php echo \yii::$app->request->baseUrl; ?>/css/register.css">
</head>

<body class="bg" style="">
<div class="box">
    <div class="txtcen top"> <img src="<?php echo \yii::$app->request->baseUrl; ?>/images/top.png" width="100%"> </div>
    <div class="wbg hid">
        <div class="bbg hid">
            <ul>
                <li class="lbg"> <img src="<?php echo \yii::$app->request->baseUrl; ?>/images/man.png" height="18px">
                    <input type="text" name="parent_user_mobile" class="inp" placeholder="<?php echo $mobile; ?>" value="<?php echo $mobile; ?>" disabled="disabled">
                    <input type="hidden" name="parent_user_id" value="<?php echo $user_id; ?>">
                    <div class="close"></div>
                </li>
                <li class="lbg"> <img src="<?php echo \yii::$app->request->baseUrl; ?>/images/tel.png" height="18px">
                    <input type="text" name="mobile" class="inp" placeholder="请输入电话号码">
                    <div class="close"></div>
                </li>
                <li class="lbg"> <img src="<?php echo \yii::$app->request->baseUrl; ?>/images/lock.png" height="18px">
                    <input type="password" name="password" class="inp" placeholder="请设置登录密码">
                    <div class="close"></div>
                </li>
                <li class="lbg"> <img src="<?php echo \yii::$app->request->baseUrl; ?>/images/lock.png" height="18px">
                    <input type="password" name="repeat_password" class="inp" placeholder="请重复登录密码">
                    <div class="close"></div>
                </li>
                <li>
                    <div class="lbg lef inbox"> <img src="<?php echo \yii::$app->request->baseUrl; ?>/images/code.png" height="18px">
                        <input type="text" name="code" placeholder="请输入验证码">
                    </div>
                    <button class="code">获取验证码</button> </li>
                <li class="lbg"> <img src="<?php echo \yii::$app->request->baseUrl; ?>/images/name.png" height="18px">
                    <input type="text" class="inp" name="name" placeholder="输入自己后台名称">
                    <div class="close"></div>
                </li>
            </ul>
            <div class="btn clr" id="register">立即注册</div>

            <a href="<?php echo Yii::$app->params['appDownloadUrl']; ?>" class="account txtcen">已有账号？<font>立即下载【开店宝助手】APP</font></a>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo \yii::$app->request->baseUrl; ?>/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript">
    var $countdown = 60;
    function settime(obj) { //发送验证码倒计时

        if ($countdown == 0) {
            obj.text("获取验证码");
            $countdown = 60;
            $(".code").attr('disabled',false)
            return;
        } else {
            $(".code").attr('disabled',true)
            obj.text("重新发送(" + $countdown + ")");
            $countdown--;

        }
        setTimeout(function() { settime(obj) },1000)
    }
    $(function(){
        $(".code").click(function(){
            if($("input[name='mobile']").val().length==11){
                settime($(this));
                var mobile=$("input[name='mobile']").val();
                var parent_user_mobile=$("input[name='parent_user_mobile']").val();
                if($(this).text() != "获取验证码"){

                    $.ajax({
                        type : "POST",
                        url:"/site/register-code",//+tab,
                        data : {mobile:mobile,parent_user_mobile:parent_user_mobile},// 你的formid
                        dataType : 'json',
                        success: function(data){
                            alert(data.message);
                        }
                    });
                }
            }else{
                alert("请输入手机号码");
            }
        });

        $("#register").click(function(){
            var parent_user=$("input[name='parent_user_mobile']").val();
            var mobile=$("input[name='mobile']").val();
            var password =$("input[name='password']").val();
            var repeat_password  =$("input[name='repeat_password']").val();
            var code     =$("input[name='code']").val();
            var name     =$("input[name='name']").val();
            var parent_user_id = $("input[name='parent_user_id']").val()
            if(mobile.length!=11){
                alert('请输入正确的手机号码');
                return false;
            }
            if(password.length < 6){
                alert('密码长度必须大于或等于6位');
                return false;
            }
            if(password!=repeat_password){
                alert('前后输入的密码不一致');
                return false;
            }
            if(name == '')
            {
                alert('请输入后台名称');
                return false;
            }
            $.ajax({
                type : "POST",
                url:"/site/register-do",//+tab,
                data : {mobile:mobile,password:password,repeat_password:repeat_password,code:code,user_name:name,parent_user_id:parent_user_id,parent_user:parent_user},// 你的formid
                dataType : 'json',
                success: function(data){
                    if(data.code == 200)
                    {
                        alert(data.message);
                    }else{
                        if(data.message.code != '' && data.message.code != undefined)
                        {
                            alert(data.message.code);
                        }
                        if(data.message.mobile != '' && data.message.mobile != undefined){
                            alert(data.message.mobile);
                        }
                    }

                    // else{
                    //     alert(data.message);
                    // }
                }
            });
        })

        $(".inp").change(function(){
            if($(this).val().length>0){
                $(this).next().show();
            }
        });

        $(".close").click(function(){
            $(this).hide().prev().val("");
        });

        $(".btn").click(function(){
            if($("input[name='password']").val()!=$("input[name='repeat_password']").val()){
                alert("重复密码不正确");
                return false;
            }else{

            }
        })

    });
</script>

</body></html>