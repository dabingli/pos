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
					<span id="modalDetailTitle">新增机具类型</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['add-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                      
	
					
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span
							class="text-danger">∗</span>机具类型:</label>
						<div class="col-md-7" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入机具类型" value="<?php echo $model->name; ?>" type="text"
								class="form-control required" maxlength="32" name="name"
								aria-required="true">
						</div>
					</div>
					
					<!--<div class="form-group">
						<label class="col-md-4 text-right control-label"><span
							class="text-danger">∗</span>贷记卡结算价(%):</label>
						<div class="col-md-7" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入贷记卡结算价" value="<?php /*echo $model->level_cc_settlement; */?>" type="text"
								class="form-control required" maxlength="32" name="level_cc_settlement"
								aria-required="true">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span
							class="text-danger">∗</span>借记卡结算价(%):</label>
						<div class="col-md-7" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入借记卡结算价" value="<?php /*echo $model->level_dc_settlement; */?>" type="text"
								class="form-control required" maxlength="32" name="level_dc_settlement"
								aria-required="true">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-4 text-right control-label"><span
							class="text-danger">∗</span>借记卡封顶结算价(元):</label>
						<div class="col-md-7" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入借记卡封顶结算价" value="<?php /*echo $model->capping; */?>" type="text"
								class="form-control required" maxlength="32" name="capping"
								aria-required="true">
						</div>
					</div>-->

                    <div class="form-group">
                        <label class="col-md-4 text-right control-label"><span
                                    class="text-danger">∗</span>激活金额:</label>
                        <div class="col-md-7" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入激活金额" value="<?php echo $model->activation_money; ?>" type="number"
                                   class="form-control required" maxlength="10" name="activation_money"
                                   aria-required="true">
                        </div>
                    </div>
					
					<div class="form-group">
						<label class="col-md-4 text-right control-label"></label>
						<div class="col-md-7" style="padding: 0px 10px 0px 0px;">
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
                'name': '请输入机具类型名称',
                'level_cc_settlement': '请输入贷记卡结算价',
                'level_dc_settlement': '请输入借记卡结算价',
                'capping': '请输入借记卡封顶结算价',
                'activation_money': '请输入激活金额',
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