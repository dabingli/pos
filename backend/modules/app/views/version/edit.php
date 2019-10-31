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
                <span id="modalDetailTitle">修改APP版本</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['version/edit-do']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="<?php echo $model->id; ?>">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>APP类型:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <?php echo Html::dropDownList('type',$model->type,[''=>'请选择']+$data['type_text'],['class'=>'form-control required']); ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>版本号:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="版本号" value="<?php echo $model->version; ?>" type="text"
                               class="form-control required" name="version"
                               aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger"></span>功能描述:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="功能描述" value="<?php echo $model->description; ?>" type="text"
                               class="form-control" name="description"
                               aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger"></span>下载地址:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="下载地址" value="<?php echo $model->url; ?>" type="text"
                               class="form-control required" name="url"
                               aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>强制更新:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <?php echo Html::dropDownList('is_allow_update',$model->is_allow_update,[''=>'请选择']+$data['is_allow_update_text'],['class'=>'form-control required']); ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>状态:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <?php echo Html::dropDownList('status',$model->status,[''=>'请选择']+$data['status_text'],['class'=>'form-control required']); ?>

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
                        url: "<?php echo Url::toRoute('version/edit-do'); ?>",     //后台处理程序
                        type: "post",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            'type': $("#form input[name='type']").val(),
                            'version' : $("#form input[name='version']").val(),
                            'description' : $("#form input[name='description']").val(),
                            'url' : $("#form input[name='url']").val(),
                            'is_allow_update' : $("#form input[name='is_allow_update']").val(),
                            'status' : $("#form input[name='status']").val(),
                        }
                    }
                }
            },
            onkeyup: false,
            messages: {
                'type': '请选择APP类型',
                'version': '请填写版本号',
                'url' : '请填写下载地址',
                'is_allow_update' : '请选择是否强制更新',
                'status' : '请选择状态'
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