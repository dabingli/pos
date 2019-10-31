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
                <span id="modalDetailTitle"><?php if($model->isNewRecord){ ?>新增<?php } ?>代理商</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['add-do']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="<?php echo $model->id; ?>">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                                class="text-danger">∗</span> 机具类型:</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <?php echo $model->name; ?>
                    </div>
                    <label class="col-md-2 text-right control-label"><span
                                class="text-danger">∗</span> 本级贷记卡结算价:</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入本级贷记卡结算价" value="<?php echo $model->level_cc_settlement; ?>" type="number" class="form-control required" maxlength="32" name="level_cc_settlement" aria-required="true">
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                                class="text-danger">∗</span> 贷记卡结算价生效日期:</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <div class='input-group date'>
                            <input id="level_cc_date" placeholder="请输入贷记卡结算价生效日期" value="<?php echo $model->level_cc_date; ?>" name="level_cc_date"  type='date' class="form-control required" />
                            <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                        </div>
                    </div>
                    <label class="col-md-2 text-right control-label"><span
                                class="text-danger">∗</span> 本级借记卡结算价(%):</label>
                    <div class="col-md-4 input-group" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入本级借记卡结算价" value="<?php echo $model->level_dc_settlement; ?>" type="number" class="form-control required" maxlength="32" name="level_dc_settlement" aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                                class="text-danger">∗</span> 借记卡封顶结算价(元):</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入借记卡封顶结算价" value="<?php echo $model->capping ?>" type="number" class="form-control required" maxlength="32" name="capping" aria-required="true">
                    </div>
                    <label class="col-md-2 text-right control-label"><span
                                class="text-danger">∗</span> 借记卡结算价生效日期:</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <div class='input-group date'>
                            <input id="level_dc_date" placeholder="请输入借记卡结算价生效日期" value="<?php echo $model->level_dc_date ?>" name="level_dc_date"  type='date' class="form-control required" />
                            <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                                class="text-danger">∗</span> 本级返现单价(元):</label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入本级返现单价" value="<?php echo $model->cash_money ?>" type="number" class="form-control required" maxlength="32" name="cash_money" aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-1 text-right control-label"></label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                    </div>
                    <div class="col-md-4">
                        <button id="submit-btn" type="submit" class="btn btn-primary">提&nbsp;&nbsp;交</button>
                        &nbsp;&nbsp;
                        <a href="<?php echo Url::toRoute(['index']); ?>" class="btn btn-default">取&nbsp;&nbsp;消</a>
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
                'name': '请填写机具类型',
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