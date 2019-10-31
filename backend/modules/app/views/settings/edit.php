<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
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
                <span id="modalDetailTitle"><?php if($is_newRecord){ ?>新增<?php }else{ ?>编辑<?php } ?>APP</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['settings/edit-do']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="<?php echo $model->id; ?>">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>APP名称:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入APP名称" value="<?php echo $model->name; ?>" type="text"
                               class="form-control required" name="name"
                               aria-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>客服电话:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入客服电话" value="<?php echo $model->mobile; ?>" type="text"
                                class="form-control required" minlength="11" name="mobile"
                                aria-required="true">
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
                'account': {
                    remote: {
                        url: "<?php echo Url::toRoute('settings/edit-do'); ?>",     //后台处理程序
                        type: "post",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            'mobile': function() {
                                return $("#form input[name='mobile']").val() ;
                            },
                            'name': function() {
                                return $("#form input[name='name']").val() ;
                            },
                            'id': function(){
                                return $("#form input[name='id']").val()
                            }
                        }
                    }
                }
            },
            onkeyup: false,
            messages: {
                'name': '请输入APP名称',
                'mobile':{
                    'required': '请输入登录账号',
                    'minlength': '密码最小长度:{0}',

                },
            },
            submitHandler: function (form) {
                var loading = $.loading();
                loading.show();
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
            }
        });
    }
    validate();
</script>