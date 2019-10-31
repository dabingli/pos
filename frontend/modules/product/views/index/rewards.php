<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use common\widgets\RegionWidget;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<div class="modal-dialog" style="width:70%">

		<div class="modal-content">

			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle">机具类型满返设置</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['rewards-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">

					<div class="form-group">
						<label class="col-md-5 text-right control-label"><span
							class="text-danger"></span> 机具类型</label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
							<input value="<?php echo $model->productType->name; ?>" class="form-control" disabled>
						</div>
					</div>
					
					<div class="form-group">
                        <label class="col-md-5 text-right control-label"><span
                                    class="text-danger">∗</span> 交易天数(自然日):</label>
                        <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入交易天数" value="<?php echo $model->return_days ?>" name="return_days" type="number" class="form-control required">
                            (自激活日算起)
                        </div>
					</div>

                    <div class="form-group">
                        <label class="col-md-5 text-right control-label"><span
                                    class="text-danger">∗</span> 累计交易金额(元):</label>
                        <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入累计交易金额" value="<?php echo $model->return_order_total_money ?>" name="return_order_total_money" type="number" class="form-control required" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-5 text-right control-label"><span
                                    class="text-danger">∗</span> 奖励金额(元/台):</label>
                        <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入奖励金额" value="<?php echo $model->return_rewards_money ?>" name="return_rewards_money" type="number" class="form-control required" >
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
            'return_days':{
                'required':true,
                'max':50000,
                'min':0,
            },
            'return_order_total_money':{
                'required':true,
                'max':99999999,
                'min':0,
            },
            'return_rewards_money':{
                'required':true,
                'max':999999,
                'min':0,
            },
        },
        messages: {
			'return_days':{
				'required':'请输入交易天数',
				'max':'本级借记卡结算价不能超过50000',
				'min':'本级借记卡结算价不能小于0',
			},
			'return_order_total_money':{
				'required':'请输入累计交易金额',
				'max':'借记卡封顶结算价不能超过99999999',
				'min':'借记卡封顶结算价不能小于0',
			},
			'return_rewards_money':{
				'required':'请输入奖励金额',
				'max':'本级返现单价不能超过999999',
				'min':'本级返现单价不能小于0',
			},
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.parent());
		},
        submitHandler: function (form) {
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
        	}
    	});
	}
	validate();
</script>