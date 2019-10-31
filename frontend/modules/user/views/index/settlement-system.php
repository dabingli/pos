<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
?>
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
					<span id="modalDetailTitle">新增结算价设置</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['settlement-system-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span class="text-danger">∗</span>机具类型</label>
						<div class="col-md-8">
						<select onchange="return agentProductType($(this))" class="form-control required" name="agent_product_type_id">
							<option value="" selected="">请选择机具类型</option>
							<?php foreach($agentProductType as $m){ ?>
							<option value="<?php echo $m->id; ?>"><?php echo $m->productType->name; ?></option>
							<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span class="text-danger">∗</span>贷记卡结算价(%)</label>
						<div class="col-md-8">
							<input min="0" placeholder="请输入贷记卡结算价(%)" value="" type="number" class="form-control required" name="level_cc_settlement" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span class="text-danger">∗</span>借记卡结算价(%)</label>
						<div class="col-md-8">
							<input min="0" placeholder="请输入借记卡结算价(%)" value="" type="number" class="form-control required" name="level_dc_settlement" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span class="text-danger">∗</span>借记卡封顶结算价(元)</label>
						<div class="col-md-8">
							<input min="0" placeholder="请输入借记卡封顶结算价(元)" value="" type="number" class="form-control required" name="capping" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span class="text-danger">∗</span>返现单价(元)</label>
						<div class="col-md-8">
							<input min="0" placeholder="请输入返现单价(元)" value="" type="number" class="form-control required" name="cash_money" aria-required="true">
						</div>
					</div>
					
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
var $agentProductType = <?php echo Json::encode($agentProductType); ?>;
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
	function validate(msg){
	 	var vali =  $("#form").validate({
        ignore: [],
        rules: {
        	
        },
        onkeyup: false,
        messages: msg,
        submitHandler: function (form) {
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
        	}
    	});
	}
	validate({
    	'agent_product_type_id': '请选择机具类型',
    	'level_cc_settlement': {
    		'required':'请输入贷记卡结算价(%)',
    		'min':'贷记卡结算价不能少于{0}'
        },
        'level_dc_settlement': {
    		'required':'请输入借记卡结算价(%)',
    		'min':'借记卡结算价不能少于{0}'
        },
        'capping': {
    		'required':'请输入借记卡封顶结算价(元)',
    		'min':'借记卡封顶结算价不能少于{0}'
        },
    	cash_money:{
			'required':'请输入返现单价(元)',
			'min':'返现单价不能少于{0}'
        }
    });
	function agentProductType(e){
		$('input[name="level_cc_settlement"]').attr('min',$agentProductType[e.val()]['level_cc_settlement']);
		$('input[name="level_dc_settlement"]').attr('min',$agentProductType[e.val()]['level_dc_settlement']);
		$('input[name="capping"]').attr('min',$agentProductType[e.val()]['capping']);
		validate();
	} 
</script>