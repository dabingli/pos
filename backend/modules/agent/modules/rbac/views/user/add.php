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
					<span id="modalDetailTitle"><?php if($model->isNewRecord){ ?>新增<?php }else{ ?>编辑<?php } ?>用户</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['/agent/rbac/user/add-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                      
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span> 机构名称:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<?php echo Html::dropDownList('agent_id',$model->agent_id,[''=>'请选择']+$agent,['class'=>'form-control required']); ?>

						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>用户名:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入用户名" value="<?php echo $model->user_name; ?>" type="text"
								class="form-control required" maxlength="32" name="user_name"
								aria-required="true">
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>登录账号:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input  value="<?php echo $model->account; ?>"  placeholder="请输入登录账号" value="" type="text"
								class="form-control required" rangelength="[3,32]" name="account"
								aria-required="true">
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
						<label class="col-md-3 text-right control-label">工号:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入工号" value="<?php echo $model->number; ?>" type="text"
								class="form-control" maxlength="32" name="number"
								aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span
							class="text-danger">∗</span>手机号码:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入手机号码" value="<?php echo $model->mobile; ?>" type="text"
								class="form-control required isInt" minlength="11" maxlength="11" name="mobile"
								aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label">邮箱:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input value="<?php echo $model->mailbox; ?>" placeholder="请输入邮箱" value="" type="email"
								class="form-control" maxlength="32" name="mailbox"
								aria-required="true">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label">备注:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<textarea placeholder="请输入备注" class="form-control" name="remarks"
								rows="5" maxlength="300"><?php echo $model->mailbox; ?></textarea>
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
            	'account': {
                	remote: {
	                url: "<?php echo Url::toRoute('/agent/rbac/user/account'); ?>",     //后台处理程序
	                type: "get",               //数据发送方式
	                dataType: "json",           //接受数据格式   
	                data: {                     //要传递的数据
	                		'account': function() {
	                				console.log(1111);
	                        		return $("#form input[name='account']").val() ;
	                    		}	               
	                 	}
                		}
	            }
            },
            onkeyup: false,
            messages: {
                'agent_id': '请选择机构名称',
    				'user_name': '请输入用户名',
    				'account':{ 
        				'required': '请输入登录账号',
        				'rangelength': '登录账号最小长度:{0}, 最大长度:{1}',
        				'remote': '该登录帐号已存在！'
        				
        			},
            		'password':{ 
            			'required':'请输入登录密码',
            			'rangelength': '密码最小长度:{0}, 最大长度:{1}'
            		},
        			'mobile': '请输入手机号码',
        			'mailbox':{ 
            			'email': '邮箱格式不正确'
            		},
            		'confirm_password':{
            			'equalTo':"两次密码输入不一致"
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
	