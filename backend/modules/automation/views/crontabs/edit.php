<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use common\widgets\RegionWidget;
?>
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
					<span id="modalDetailTitle">编辑任务:<?php echo $model->name; ?></span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal" action="<?php echo Url::toRoute(['edit-do']); ?>" method="post"
					novalidate="novalidate">
					<input type="hidden" name="id" value="<?php echo $model->id; ?>">
<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span class="text-danger">∗</span> 任务名称:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input disabled="disabled"  value="<?php echo $model->name; ?>" type="text" class="form-control"  maxlength="32" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span class="text-danger">∗</span> 当前总任务数量:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input name="num" value="<?php echo $num; ?>" type="number" class="form-control required" range="[0,60]" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span class="text-danger">∗</span> 进行中总任务数量:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input disabled="disabled"  value="<?php echo $numing; ?>" type="number" class="form-control" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span class="text-danger">∗</span> 任务操作:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input disabled="disabled"  value="<?php echo $model->route; ?>" placeholder="请输入任务操作,例如：queue/run" value="" type="text" class="form-control" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"><span class="text-danger">∗</span> 任务时间格式:</label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<input disabled="disabled"  value="<?php echo $model->crontab; ?>" type="text" class="form-control required" aria-required="true">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"></label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
						</div>
						<div class="col-md-4">
							<button id="submit-btn" type="submit" class="btn btn-primary">提&nbsp;&nbsp;交</button>
							&nbsp;&nbsp;
							<a href="<?php echo Url::toRoute(['index']); ?>" class="btn btn-default">取&nbsp;&nbsp;消</a>
						</div>
					</div>

				</form>
					<table class="table table-striped table-bordered dataTable">
						<thead>
            					<tr>
            						<th width="10%">时间格式</th>
                                    <th width="80%">命令</th>
                                    <th width="10%">关键字</th>
            					</tr>
            			</thead>
            				<tbody>
                              <?php if (!empty($jobs)) : ?>
                              <?php foreach ($jobs as $job) : ?>
                                <tr>
                                  <td><?php echo $job->datetimeFormat ?></td>
                                  <td><?php echo $job->taskCommandLine ?></td>
                                  <td><?php echo $job->comments ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                              </tbody>
            			</table>
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