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
					<span id="modalDetailTitle">新增权限管理</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['/agent/rbac/role/add-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                      
	
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>权限名称:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入权限名称" value="<?php echo $model->name; ?>" type="text"
								class="form-control required" maxlength="32" name="name"
								aria-required="true">
						</div>
					</div>
				
					<div class="form-group">
						<label class="col-md-3 text-right control-label">备注:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<textarea placeholder="请输入备注" class="form-control" name="description"
								rows="5" maxlength="300"><?php echo $model->description; ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>选择机构:</label>
						<div class="col-md-8" style="padding: 6px 10px 0px 0px;">
							<?php $agentArr = array_column($model->mechanismRoleEnterprise,'agent_id'); ?>
							<?php foreach($agent as $k=>$m){ ?>
					
							<?php if(in_array($k, $agentArr)){ ?>
							<input checked="checked" value="<?php echo $k; ?>" type="checkbox"  name="agent_id[]" />
							<?php }else{ ?>
							<input  value="<?php echo $k; ?>" type="checkbox"  name="agent_id[]" />
							<?php } ?>
							

							<?php echo $m; ?>
							<?php } ?>
							<br />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"></label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
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
                	'agent_id': '请选择机构名称',
    				'name': '请输入角色名称',
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