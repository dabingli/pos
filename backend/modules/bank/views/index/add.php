<?php
use yii\helpers\Url;
use common\models\user\BankCard;
use common\helpers\HtmlHelper;
?>

<div class="modal-dialog" style="width:60%">

		<div class="modal-content">

			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle">新增银行</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="addForm" onsubmit="return false" action="<?php echo Url::toRoute(['add-do']) ?>" method="post" class="form-horizontal"
					novalidate="novalidate">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">

                    <div class="form-group">
                        <label class="col-md-5 text-right control-label"> 排序:</label>
                        <div class="col-md-5" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入排序" value="0" type="number" class="form-control required" name="order" aria-required="true">
                        </div>
                    </div>

                    <div class="form-group">
						<label class="col-md-5 text-right control-label"><span
							class="text-danger">∗</span> 银行简称:</label>
						<div class="col-md-5" style="padding: 0px 10px 0px 0px;">
							<input placeholder="请输入银行简称 如：ABC" value="" type="text" class="form-control required" maxlength="32" name="bank" aria-required="true">
						</div>
					</div>

                    <div class="form-group">
                        <label class="col-md-5 text-right control-label"><span
                                    class="text-danger">∗</span> 银行名称:</label>
                        <div class="col-md-5" style="padding: 0px 10px 0px 0px;">
                            <input placeholder="请输入银行名称" value="" type="text" class="form-control required" maxlength="32" name="name" aria-required="true">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-5 text-right control-label"> 银行logo:</label>
                        <div class="col-md-5" style="padding: 5px 10px 0px 0px;">
                            <p><img id="bank-img-add" class="img-circle rf-img-md" src="" data-toggle="modal" data-target="#avatar-modal"></p>
                            <input type="hidden" name="logo" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-5 text-right control-label"> 状态:</label>
                        <div class="col-md-4" style="padding: 7px 10px 0px 0px;">
                            <div id="agentmenu-dev" class="padding-text">
                                <?php foreach (BankCard::statusLabels() as $k=>$v) { ?>
                                    <label style="margin-right: 15px"><input type="radio" name="status" value="<?=$k?>" checked=""> <?=$v?> </label>
                                <?php } ?>
                            </div>
                        </div>

                    </div>

					<div class="form-group">
						<label class="col-md-1 text-right control-label"></label>
						<div class="col-md-4" style="padding: 10px 10px 0px 0px;">
						</div>
						<div class="col-md-4">
							<button id="submit-btn" type="submit" class="btn btn-primary">提&nbsp;&nbsp;交</button>
							&nbsp;&nbsp;
							<a href="<?php echo Url::toRoute(['index']); ?>" class="btn btn-default">返&nbsp;&nbsp;回</a>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="msg-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button  type="button" class="btn btn-primary" data-dismiss="modal">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script>

	function validate(){
	 	$("#addForm").validate({
        ignore: [],
        rules: {
        	'bank' : {
        	    'required' : true
            },
            'name' : {
                'required' : true
            }
        },
        messages: {
            'bank': {
                'required' : '请输入银行简称'
            },
            'name': {
                'required' : '请输入银行名称'
            }
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.parent());
		},
        submitHandler: function (form) {
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
        	}
    	});
	}
	validate();

</script>