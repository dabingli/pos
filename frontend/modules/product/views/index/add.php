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
<div class="modal-dialog" style="width:85%">


		<div class="modal-content">


			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle"><?php if($model->isNewRecord){ ?>新增<?php }else{ ?>修改<?php } ?>机具类型</span>&nbsp;
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
							<?php if($model->isNewRecord){ ?>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
						
						<select name="product_type_id" class="form-control required">
                                <option value="">请选择</option>
                                <?php
                                    foreach($type as $key=> $val){
                                ?>
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php }?>
                            </select>
							
						</div>
						<?php }else{ ?>
						<div class="col-md-4" style="padding: 8px 10px 0px 0px;">
							<?php echo $model->productType->name ?>
							</div>
                            <?php } ?>
						<label class="col-md-2 text-right control-label"><span
							class="text-danger">∗</span> 本级贷记卡结算价(%):</label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
							<input min="0" placeholder="请输入本级贷记卡结算价" <?php if(isset($model->userSettlement->maxLevelCcSettlement->level_cc_settlement)){ ?> max="<?php echo $model->userSettlement->maxLevelCcSettlement->level_cc_settlement; ?>" <?php } ?> value="<?php echo $model->level_cc_settlement; ?>" type="number" class="form-control required" maxlength="32" name="level_cc_settlement" aria-required="true">
						</div>
						
					</div>

					<div class="form-group">
						<label class="col-md-2 text-right control-label"><span
							class="text-danger">∗</span> 本级借记卡结算价(%):</label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
							<input min="0" placeholder="请输入本级借记卡结算价" <?php if(isset($model->userSettlement->maxLevelDcSettlement->level_dc_settlement)){ ?> max="<?php echo $model->userSettlement->maxLevelDcSettlement->level_dc_settlement; ?>" <?php } ?> value="<?php echo $model->level_dc_settlement; ?>" type="number" class="form-control required" maxlength="32" name="level_dc_settlement" aria-required="true">
						</div>
                        <label class="col-md-2 text-right control-label"><span
                                    class="text-danger">∗</span> 借记卡封顶结算价(元):</label>
                        <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                            <input min="0" placeholder="请输入借记卡封顶结算价" <?php if(isset($model->userSettlement->maxCapping->capping)){ ?> max="<?php echo $model->userSettlement->maxCapping->capping; ?>" <?php } ?> value="<?php echo $model->capping ?>" type="number" class="form-control required" maxlength="32" name="capping" aria-required="true">
                        </div>
					</div>
					
					<div class="form-group">
                        <label class="col-md-2 text-right control-label"><span
                                    class="text-danger">∗</span> 本级返现单价(元):</label>
                        <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                            <input min="0" placeholder="请输入本级返现单价" value="<?php echo $model->cash_money ?>" type="number" class="form-control required" maxlength="32" name="cash_money" aria-required="true">
                        </div>
                        <label class="col-md-2 text-right control-label"><span
                                    class="text-danger">∗</span> 过期冻结金额(元):</label>
                        <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                            <input min="0" placeholder="请输入过期冻结金额" value="<?php echo $model->frozen_money ?>" type="number" class="form-control required" maxlength="32" name="frozen_money" aria-required="true">
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
            'product_type_id': '请选择机具类型',
			'level_cc_settlement':{
				'required':'请输入本级贷记卡结算价',
				'max':'本级贷记卡结算价不能超过{0}',
				'min':'本级贷记卡结算价不能小于{0}',
			},
			'level_dc_settlement':{
				'required':'请输入本级借记卡结算价',
				'max':'本级借记卡结算价不能超过{0}',
				'min':'本级借记卡结算价不能小于{0}',
			},
			'capping':{
				'required':'请输入借记卡封顶结算价',
				'max':'借记卡封顶结算价不能超过{0}',
				'min':'借记卡封顶结算价不能小于{0}',
			},
			'cash_money':{
				'required':'请输入本级返现单价',
				'max':'本级返现单价不能超过{0}',
				'min':'本级返现单价不能小于{0}',
			},
        },
        errorPlacement: function(error, element) {
			if (element.prop('id') == 'level_cc_date' || element.prop('id') == 'level_dc_date' || element.prop('id') == 'cash_money_date') {
				error.appendTo(element.parent().parent());
			}
			else {
				error.appendTo(element.parent());
			}
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