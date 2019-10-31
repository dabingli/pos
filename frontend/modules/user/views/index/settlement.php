<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<div class="modal-dialog"  style="width:65%">


		<div class="modal-content">


			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle">结算价设置</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['settlement-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					<table class="table table-striped table-bordered dataTable no-footer">
						<tr>
							<th>代理商编号:</th>
							<td><?php echo $model->user_code; ?></td>
							<th>代理商:</th>
							<td><?php echo $model->user_name; ?></td>
						</tr>
					</table>
					<h4>机具类型:</h4>
					
					<table class="table table-striped table-bordered dataTable no-footer">
						<tr>
							<th>机具类型</th>
							<th>贷记卡结算价(%)</th>
							<th>借记卡结算价(%)</th>
							<th>借记卡封顶结算价(元)</th>
							<th>返现单价(元)</th>

						</tr>
						<?php foreach($agentProductType as $val){ ?>
						<tr>
							<td><?php echo $val['agentProductType']['productType']['name']; ?></td>
							<td><input placeholder="请输入贷记卡结算价(%)" value="<?php echo $val['level_cc_settlement']; ?>" type="number" <?php if(isset($val->maxLevelCcSettlement->level_cc_settlement)){ ?>max="<?php echo $val->maxLevelCcSettlement->level_cc_settlement; ?>" <?php } ?> min="<?php echo $val->min->level_cc_settlement; ?>" class="form-control required"  name="data[<?php echo $val['agent_product_type_id'] ?>][level_cc_settlement]"></td>
							<td><input placeholder="请输入借记卡结算价(%)" value="<?php echo $val['level_dc_settlement']; ?>" type="number" <?php if(isset($val->maxLevelCcSettlement->level_dc_settlement)){ ?>max="<?php echo $val->maxLevelDcSettlement->level_dc_settlement; ?>" <?php } ?> min="<?php echo $val->min->level_dc_settlement; ?>" class="form-control required"  name="data[<?php echo $val['agent_product_type_id'] ?>][level_dc_settlement]"></td>
							<td><input placeholder="请输入借记卡封顶结算价(元)" value="<?php echo $val['capping']; ?>" type="number" <?php if(isset($val->maxCapping->capping)){ ?>max="<?php echo $val->maxCapping->capping; ?>" <?php } ?> min="<?php echo $val->min->capping; ?>" class="form-control required"  name="data[<?php echo $val['agent_product_type_id'] ?>][capping]"></td>
							<td><input placeholder="请输入返现单价(元)" value="<?php echo $val['cash_money']; ?>" type="number" <?php if(isset($val->minCashMoney->cash_money)){ ?>min="<?php echo $val->minCashMoney->cash_money; ?>" <?php } ?> class="form-control required"  name="data[<?php echo $val['agent_product_type_id'] ?>][cash_money]"></td>
							</tr>
						<?php } ?>
					</table>
					
					
					<div class="form-group">
						<label class="col-md-1 text-right control-label"></label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
						</div>
						<div class="col-md-4">
							<button id="submit-btn" type="submit" class="btn btn-primary">提&nbsp;&nbsp;交</button>
							
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
<script>
	jQuery.extend(jQuery.validator.messages, {
	  required: "必选字段",
	  remote: "请修正该字段",
	  email: "请输入正确格式的电子邮件",
	  url: "请输入合法的网址",
	  date: "请输入合法的日期",
	  dateISO: "请输入合法的日期 (ISO).",
	  number: "请输入合法的数字",
	  digits: "只能输入整数",
	  creditcard: "请输入合法的信用卡号",
	  equalTo: "请再次输入相同的值",
	  accept: "请输入拥有合法后缀名的字符串",
	  maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
	  minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
	  rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
	  range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
	  max: jQuery.validator.format("请输入一个最大为{0} 的值"),
	  min: jQuery.validator.format("请输入一个最小为{0} 的值")
	});
	function validate(){
	 	var vali =  $("#form").validate({
        ignore: [],
        rules: {
        	
        },
        onkeyup: false,
        messages: {
            
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