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
					<span id="modalDetailTitle">修改上级所属上级</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['edit-parent-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					<div class="form-group">
						<label class="col-md-2 text-right control-label">代理商编号:</label>
						<div class="col-md-4" style="padding: 7px 10px 0px 0px;">
							<?php echo $model->user_code; ?>
						</div>
						<label class="col-md-2 text-right control-label">代理商:</label>
						<div class="col-md-4" style="padding: 7px 10px 0px 0px;">
							<?php echo $model->user_name; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 text-right control-label"> 修改前上级编号:</label>
						<div class="col-md-4" style="padding: 7px 10px 0px 0px;">
							<?php echo isset($model->parent->user_code)?$model->parent->user_code:''; ?>
						</div>
						<label class="col-md-2 text-right control-label">修改前上级代理商:</label>
						<div class="col-md-4" style="padding: 7px 10px 0px 0px;">
							<?php echo isset($model->parent->user_name)?$model->parent->user_name:''; ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-2 text-right control-label"><span
							class="text-danger">∗</span> 修改后上级编号:</label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入修改后上级编号" value="" type="text" class="form-control required" name="parent_user_code" aria-required="true">
						</div>
						<label class="col-md-2 text-right control-label"><span
							class="text-danger">∗</span> 修改后上级代理商:</label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
							<input disabled id="parent"  value="" type="text" class="form-control"  aria-required="true">
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
	
	

	function validate(){
	 	var vali =  $("#form").validate({
        ignore: [],
        rules: {
        	'parent_user_code': {
        		remote: {
      	          type: "get",
      	          url: "<?php echo Url::toRoute('account'); ?>",
      	          data: {
        	        	parent_user_code: function() {
      	            		return $("#form input[name='parent_user_code']").val() ;
      	            	},
      	            	'id':<?php echo $model->id; ?>
      	          },
      	          dataType: "json",
      	          dataFilter: function(data, type) {
        	        	//console.log(data);
        	        	//console.log(type);
        	        	jon = JSON.parse(data);
        	        	if(jon.code==0){
        	        		$('#parent').val('');
							return false;
        	        	}
        	        	$('#parent').val(jon['data']['user_name'])
      	            	return true;
      	          }
      	        }
            }
        },
        onkeyup: false,
        messages: {
            'parent_user_code':{ 
    			'required':'请输入修改后上级编号',
    			'remote':'该上级编号不存在或者所属关系无法向下级所属'
    		},
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