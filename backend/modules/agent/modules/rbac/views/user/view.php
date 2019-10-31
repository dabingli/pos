<?php 
use yii\helpers\Html;
use yii\helpers\Url;
?>
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
					<span id="modalDetailTitle">用户详情</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form novalidate="novalidate">

					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span> 机构名称:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo isset($model->agent->name)?$model->agent->name:''; ?>

						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label">机构编号:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo isset($model->agent->number)?$model->agent->number:''; ?>

						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>用户名:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->user_name; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>登录账号:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->account; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label">状态:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->getStatus(); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label">工号:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->number; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>手机号码:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->mobile; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label">邮箱:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->mailbox; ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label">备注:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->remarks; ?>
						</div>
					</div>
					

				</form>
			</div>
		</div>
	</div>
