<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use common\widgets\RegionWidget;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<div class="modal-dialog" style="width:55%">


    <div class="modal-content">


        <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
                                                       class="glyphicon glyphicon-remove-sign"
                                                       style="font-size: 18px; cursor: pointer;"></span></b></span>
            </button>
            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">设置手续费</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['/agent/index/fee-edit-do']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="<?php echo $model->id; ?>">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                <div class="form-group">
                    <label class="col-md-5 control-label"><span
                            class="text-danger">∗</span> 提现手续费（元/笔）:</label>
                    <div class="col-md-5">
                        <input placeholder="手续费" value="<?php echo $model->agent_fee; ?>" type="number" class="form-control required" name="agent_fee" aria-required="true">
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
    function validate(){
        $("#form").validate({
            ignore: [],
            rules: {

            },
            messages: {
                'agent_fee': '请输入手续费',
            },
            submitHandler: function (form) {
                /*var loading = $.loading();
                loading.show();*/
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
            }
        });
    }
    validate();
</script>