<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use common\widgets\RegionWidget;
?>

<div class="modal-dialog" style="width:55%">


		<div class="modal-content">


			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle">新增任务</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal" action="<?php echo Url::toRoute(['add-do']); ?>" method="post"
					novalidate="novalidate">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					
					<div class="form-group">
						<label class="col-md-2 text-right control-label"><span class="text-danger">∗</span> 任务名称:</label>
						<div class="col-md-10" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入任务名称,例如：消息队列" value="" type="text" class="form-control required"  maxlength="32" name="name" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 text-right control-label"><span class="text-danger">∗</span> 任务数量:</label>
						<div class="col-md-10" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入任务数量，例如：10" value="" type="number" class="form-control required" range="[0,60]" name="num" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 text-right control-label"><span class="text-danger">∗</span> 任务操作:</label>
						<div class="col-md-10" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入任务操作,例如：queue/run" value="" type="text" class="form-control required" name="route" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 text-right control-label"><span class="text-danger">∗</span> 任务时间格式:</label>
						<div class="col-md-2" style="padding: 0px 10px 0px 0px;">
							<input placeholder="分钟" value="*" type="text" class="form-control required" name="minutes" aria-required="true">
						</div>
						<div class="col-md-2" style="padding: 0px 10px 0px 0px;">
							<input placeholder="小时" value="*" type="text" class="form-control required" name="hours" aria-required="true">
						</div>
						<div class="col-md-2" style="padding: 0px 10px 0px 0px;">
							<input placeholder="天" value="*" type="text" class="form-control required" name="dayOfMonth" aria-required="true">
						</div>
						<div class="col-md-2" style="padding: 0px 10px 0px 0px;">
							<input placeholder="月" value="*" type="text" class="form-control required" name="months" aria-required="true">
						</div>
						<div class="col-md-2" style="padding: 0px 10px 0px 0px;">
							<input placeholder="周" value="*" type="text" class="form-control required" name="dayOfWeek" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 text-right control-label"> 备注:</label>
						<div class="col-md-10" style="padding: 0px 10px 0px 0px;">
							<textarea placeholder="请输入备注，例如：该队列是消息队列请勿删除" class="form-control" name="remarks"></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-2 text-right control-label"></label>
						<div class="col-md-10" style="padding: 0px 10px 0px 0px;">
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
	
	function validate(){
	 	$("#form").validate({
        ignore: [],
        rules: {
        	
        },
        messages: {
            'name': '请输入任务名称',
			'route':'请输入任务操作',
			'crontab':'请输入任务时间格式',
			'remarks':'请输入备注',
			'num':{ 
    			'required':'请输入任务数量',
    			'range': '请输入范围在 {0} 到 {1} 之间的数值'
    		},
        },
        submitHandler: function (form) {
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