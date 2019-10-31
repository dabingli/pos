<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\agent\Agent;

$this->title = '提现设置';
$this->params['breadcrumbs'][] = '系统管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>

<style>
    .unit-cs{
        width: 80%;
        display: inline;
    }
</style>
<link href="<?= \yii::$app->request->baseUrl?>/resources/bootstrap/bootstrap-switch/css/bootstrap2/bootstrapSwitch.css" rel="stylesheet" />

<script src="<?=\yii::$app->request->baseUrl?>/resources/bootstrap/bootstrap-switch/js/bootstrapSwitch.js"></script>

<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<div class="row">
<div class="col-sm-12">

   <div class="box">
       <div class="panel-body">

           <form class="form-horizontal">
               <div class="item form-group">
                   <label class="control-label col-md-3 col-sm-3 col-xs-12" >是否启用提现设置：
                   </label>
                   <div class="col-md-3 col-sm-3 col-xs-12">
                       <div class="switch" data-name="status" data-on="success" data-off="danger">
                           <input type="checkbox" <?= $model->cash_status == Agent::CASH_STATUS_OPEN ? 'checked' : '' ?>/>
                       </div>
                   </div>
               </div>
           </form>

           <hr>

            <form id="myForm" action="<?php echo Url::toRoute('index'); ?>" method="post" class="form-horizontal" <?= $model->cash_status == Agent::CASH_STATUS_CLOSE ? 'style="display:none;"' : '' ?>>
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                  <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" >提现手续费(返现)：
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                      <input min="0" value="<?php echo $model->cashback_fee; ?>" type="text" placeholder="请输入提现手续费"  name="cashback_fee" class="form-control required unit-cs"> 元/笔
                    </div>
                  </div>
                  <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" >最小提现金额(返现)：
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                      <input min="0" value="<?php echo $model->min_cashback; ?>" type="text" placeholder="请输入最小提现金额"  name="min_cashback" class="form-control required unit-cs"> 元
                    </div>
                  </div>
                <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" >提现税点(返现)：
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input min="0" value="<?php echo $model->cashback_tax_point; ?>" type="text"  name="cashback_tax_point"  placeholder="请输入提现税点" class="form-control required unit-cs"> %
                    </div>
                </div>

                <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">提现手续费(分润)：
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                      <input min="0" value="<?php echo $model->cash_fee; ?>" type="text"  name="cash_fee" placeholder="请输入提现手续费" class="form-control required unit-cs"> 元/笔
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" >最小提现金额(分润)：
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                      <input min="0" value="<?php echo $model->min_cash_amount; ?>" type="text" name="min_cash_amount"  placeholder="请输入最小提现金额" class="form-control required unit-cs"> 元
                    </div>
                  </div>

                <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" >提现税点(分润)：
                    </label>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <input min="0" value="<?php echo $model->tax_point; ?>" type="text"  name="tax_point"  placeholder="请输入提现税点" class="form-control required unit-cs"> %
                    </div>
                </div>

                  <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                      <button type="submit" class="btn btn-success">提交</button>
                      <a href="<?php echo Url::toRoute(['index']); ?>" class="btn btn-primary">重置</a>
                    </div>
                  </div>

            </form>
        </div>
   </div>
</div>
</div>
<script type="text/javascript">

    // 修改 状态
    $('.switch').on('switch-change', function (e, data) {

        var isChecked= $(data.el).prop('checked');

        var status = isChecked ? '<?= Agent::CASH_STATUS_OPEN ?>' : '<?= Agent::CASH_STATUS_CLOSE ?>';

        $.ajax({
            type: 'POST',
            url: "<?php echo Url::toRoute('ajax-edit-status') ?>",
            data: {'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'status':status},
            dataType: 'JSON',
            success: function (res) {
                if(res.status == 200){
                    if(isChecked){
                        $('#myForm').show();
                    } else {
                        $('#myForm').hide();
                    }
                } else {
                    jQuery(function ($) {
                        toastr.error("设置失败")
                    });

                    var _switch = $('.switch').find('.switch-animate');

                    if(isChecked){
                        $(data.el).prop('checked', false);
                        _switch.addClass('switch-off');
                        _switch.removeClass('switch-on');
                    } else {
                        $(data.el).prop('checked', true);
                        _switch.addClass('switch-on');
                        _switch.removeClass('switch-off');
                    }
                }
            },
            error: function () {
                rfError('保存失败啦!');
            }
        });
    });

    function validate(){

        $("#myForm").validate({
            ignore: [],
            rules: {

            },
            onkeyup: false,
            messages: {
                'tax_point': '请输入提现税点（分润）',
                'min_cash_amount': {
                    min : '请输入大于{0}的数',
                    required : '请输入最小提现金额(分润)'
                },
                'cash_fee' : '请输入提现手续费',
                'cashback_fee' : '请输入提现手续费',
                'min_cashback' : {
                    min : '请输入大于{0}的数',
                    required : '请输入最小提现金额(返现)'
                },
                'cashback_tax_point' : '请输入提现税点（返现）'
            },
            submitHandler: function (form) {
                // var loading = $.loading();
                // loading.show();
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
            }
        });
    }
    validate();

</script>