<?php
use yii\helpers\Url;
use common\models\agent\AgentRechargeLog;

?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<style>
    .padding-text{
        padding: 7px;
    }
</style>

<div class="modal-dialog" style="width:60%">

    <div class="modal-content">


        <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
                                                       class="glyphicon glyphicon-remove-sign"
                                                       style="font-size: 18px; cursor: pointer;"></span></b></span>
            </button>
            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">代理商充值</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['/agent/recharge/recharge']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="<?=$agentInfo['id']?>">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                <div class="form-group">
                    <label class="col-md-3 text-right control-label">代理商姓名:</label>
                    <div class="col-md-6 padding-text">
                        <?=$agentInfo['name']?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 text-right control-label">剩余代付金:</label>
                    <div class="col-md-3 padding-text">
                        <?=$agentInfo['balance']?>元
                    </div>

                    <label class="col-md-3 text-right control-label">剩余短信:</label>
                    <div class="col-md-3 padding-text">
                        <?=$agentInfo['remaining_sms_number']?>条
                    </div>
                </div>

                <div class="form-group" style="height: 20px">

                </div>

                <div class="form-group">
                    <label class="col-md-5 text-right control-label"><span
                                class="text-danger">∗</span> 充值类型:</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <div id="agentmenu-dev" class="padding-text">
                            <?php foreach ($typeList as $k=>$v) { ?>
                            <label style="margin-right: 15px"><input type="radio" name="type" value="<?=$k?>" checked=""> <?=$v?> </label>
                            <?php } ?>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-md-5 text-right control-label"><span
                            class="text-danger">∗</span> 充值金额:</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入充值金额" value="" type="number" class="form-control required" maxlength="32" name="money" aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-1 text-right control-label"></label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                    </div>
                    <div class="col-md-4">
                        <button id="submit-btn" type="submit" class="btn btn-primary">提&nbsp;&nbsp;交</button>
                        &nbsp;&nbsp;
                        <a href="<?php echo Url::toRoute(['/agent/index/index']); ?>" class="btn btn-default">取&nbsp;&nbsp;消</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<script>


    $('.date').datetimepicker({
        language: 'zh-CN',
        minView: 4,
        autoclose: true,
        format : 'yyyy-mm-dd'
    });
    $('#typeTable').on('click', '.remove', function () {
        $('#typeTable .'+$(this).parent().attr('data-id')+' input').val('');
    });
    function validate(){
        $("#form").validate({
            ignore: [],
            rules: {

            },
            messages: {
                'type': '请选择充值类型',
                'money':'请输入充值金额',
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
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