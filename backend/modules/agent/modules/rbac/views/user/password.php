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
					<span id="modalDetailTitle">用户: <?php echo $model->user_name?> 重置密码</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['/agent/rbac/user/password-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                      
				
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>登录账号:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;margin-top:8px;">
							<?php echo $model->account; ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>登录密码:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input id="password" placeholder="请输入登录密码" value="" type="password"
								class="form-control required" rangelength="[6,32]" name="password"
								aria-required="true">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>确认登录密码:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入登录密码" value="" type="password"
								class="form-control"  name="confirm_password"
								aria-required="true" equalTo="#password">
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
                
            		'password':{ 
            			'required':'请输入登录密码',
            			'rangelength': '密码最小长度:{0}, 最大长度:{1}'
            		},
        			
            		'confirm_password':{
            			'equalTo':"两次密码输入不一致"
                	}
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
	