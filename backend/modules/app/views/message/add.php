<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\app\AppMessage;
?>

<div class="modal-dialog">


    <div class="modal-content" id="detail_body">


        <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
                                                       class="glyphicon glyphicon-remove-sign"
                                                       style="font-size: 18px; cursor: pointer;"></span></b></span>
            </button>
            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">新增APP消息</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"  action="<?php echo Url::toRoute(['add-do']); ?>" method="post">

                <input type="hidden" name="id" value="">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">

				<div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>消息类型:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <?php echo Html::dropDownList('type','',[''=>'请选择',AppMessage::SYSTEM=>'系统',AppMessage::NOTICE=>'公告'],['class'=>'form-control required']); ?>

                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>消息名称:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="消息名称" value="" type="text" class="form-control required" name="title" aria-required="true">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>消息内容:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <textarea rows="10" cols="30" placeholder="消息内容" class="form-control required" name="content" ></textarea>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>通知群体:</label>
                    <div class="col-md-8" style="padding: 7px 10px 0px 0px;">
                    	<div class="col-md-4">
                    		<input onClick="return receiver($(this))" value="1" type="radio"  checked="checked"  name="receiver_name">全部
                    	</div>
                        <div class="col-md-4">
                    		<input onClick="return receiver($(this))" value="0" type="radio" name="receiver_name">某个商户
                    	</div>
                    </div>
                </div>
                <div class="form-group" id="user_code" style="display: none;">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>商户编码:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="商户编码" value="" type="text" class="form-control" name="user_code" aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"></label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <button  type="submit" class="btn btn-primary" id="submit-btn">提&nbsp;&nbsp;交</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    function validate(){
        $("#form").validate({
            ignore: [],
            rules: {
                
            },
            onkeyup: false,
            messages: {
                'title': '消息名称不能为空',
                'content':'消息内容不能为空',
                'type' : '请选择消息类型',
                'user_code' : '请输入商户编码',
            },
            submitHandler: function (form) {
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
            }
        });
    }
    validate();
    function receiver(e){
		if(e.val()==0){
			$("#user_code input").attr('class','form-control required');
			$("#user_code").show();
		}else{
			
			$("#user_code").hide();
			$("#user_code input").attr('class','form-control');
		}
    }
</script>