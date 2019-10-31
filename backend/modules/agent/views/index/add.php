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
					<span id="modalDetailTitle"><?php if($model->isNewRecord){ ?>新增<?php } ?>服务商</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['/agent/index/add-do']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					<div class="form-group">
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 服务商名称:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入服务商名称" value="<?php echo $model->name; ?>" type="text" class="form-control required" maxlength="32" name="name" aria-required="true">
						</div>
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 服务商编号:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入服务商编号" value="<?php echo $model->number; ?>" type="text" class="form-control required" maxlength="32" name="number" aria-required="true">
						</div>
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 签约日期:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<div class='input-group date'>
                                        <input id="contract_date" placeholder="请输入签约日期" value="<?php echo Yii::$app->request->get('contract_date'); ?>" name="contract_date"  type='date' class="form-control required" />
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                              </div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 归属省:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<?php echo RegionWidget::widget(['name'=>'province_id','region_id'=>$model->province_id,'options'=>['class'=>'form-control required','onchange'=>'city($(this),"city_id")']]); ?>
							
						</div>
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 归属市:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<?php echo RegionWidget::widget(['name'=>'city_id','region_id'=>$model->city_id?$model->city_id:false,'options'=>['class'=>'form-control required','id'=>'city_id','onchange'=>'city($(this),"county_id")']]); ?>
							
						</div>
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 归属区:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<?php echo RegionWidget::widget(['name'=>'county_id','region_id'=>$model->county_id?$model->county_id:false,'options'=>['class'=>'form-control required','id'=>'county_id']]); ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 联系人:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入联系人" value="<?php echo $model->contacts; ?>" type="text" class="form-control required"  maxlength="32" name="contacts" aria-required="true">
						</div>
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 联系电话:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入联系电话" value="<?php echo $model->mobile; ?>" type="tel" class="form-control required" rangelength="[11,11]" maxlength="32" name="mobile" aria-required="true">
						</div>
						<label class="col-md-1 text-right control-label"><span
							class="text-danger">∗</span> 联系邮箱:</label>
						<div class="col-md-3" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入联系邮箱" value="<?php echo $model->mailbox; ?>" type="email" class="form-control required" maxlength="32" name="mailbox" aria-required="true">
						</div>
					</div>
					
					<div class="form-group">

                        <label class="col-md-1 text-right control-label"><span
                                    class="text-danger">∗</span> 服务商后台名称:</label>
                        <div class="col-md-3" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入服务商后台名称" value="<?php echo $model->admin_name; ?>" type="text" class="form-control required" maxlength="6" name="admin_name" aria-required="true">
                        </div>

                        <label class="col-md-1 text-right control-label"><span
                                    class="text-danger">∗</span> 联系地址:</label>
                        <div class="col-md-7" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入联系地址" value="<?php echo $model->address; ?>" type="text" class="form-control required" maxlength="32" name="address" aria-required="true">
                        </div>
					</div>
					<div class="form-group">
						<label class="col-md-1 text-right control-label"></label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
						</div>
						<div class="col-md-4">
							<button id="submit-btn" type="submit" class="btn btn-primary">提&nbsp;&nbsp;交</button>
							&nbsp;&nbsp;
							<a href="<?php echo Url::toRoute(['/agent/index/index']); ?>" class="btn btn-default">取&nbsp;&nbsp;消</a>
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
            'name': '请选择服务商名称',
            'admin_name': '请输入服务商后台名称',
			'number':'请输入服务商编号',
			'contract_date': '请输入签约日期',
			'contacts': '请输入联系人',
			'mobile':{ 
    			'required':'请输入联系电话',
    			'rangelength': '联系电话最小长度:{0}, 最大长度:{1}'
    		},
			'mailbox':'请输入联系邮箱',
			'address': '请输入联系地址',
			'province_id':'请选择省份',
			'city_id':'请选择市',
			'county_id':'请选择归属区',
			'county_id':'请选择归属区',
        },
        errorPlacement: function(error, element) {
			if (element.prop('id') == 'contract_date') {
				error.appendTo(element.parent().parent());
			}
			else {
				error.appendTo(element.parent());
			}
		},
        submitHandler: function (form) {
                /*var loading = $.loading();
                loading.show();*/
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
        	}
    	});
	}
	validate();
	function city(e,id){
		 $.ajax({
	            type:"GET",
	            url:"<?php echo Url::toRoute(['/site/region']) ?>?region_id="+e.val(),
	            dataType:'json',
	            success:function(result){
		            var s = '<option value="" selected="">请选择</option>'
					for(var r in result['data']){
						s+=`<option value="`+result['data'][r]['id']+`">`+result['data'][r]['title']+`</option>`
					}
					if(id!=''){
						$('#'+id).html(s);
					}
	            }
	      });
	}
</script>