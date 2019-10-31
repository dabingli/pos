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
                <span id="modalDetailTitle">详情</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <div id="form" class="form-horizontal">

                <input type="hidden" name="id" value="<?php echo $model['id']; ?>">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                            class="text-danger">∗</span> 姓名:</label>
                        <div class="col-md-4" style="padding: 8px 10px 0px 0px;">
                            <?php echo $model['real_name'] ?>
                        </div>
                    <label class="col-md-2 text-right control-label"><span
                            class="text-danger">∗</span> 代理商编号:</label>
                    <div class="col-md-4" style="padding: 8px 10px 0px 0px;">
                        <?php echo $model['user']['user_code'] ?>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                            class="text-danger">∗</span> 身份证号码:</label>
                    <div class="col-md-4" style="padding: 8px 10px 0px 0px;">
                        <?php echo $model['identity_card'] ?>
                    </div>
                    <label class="col-md-2 text-right control-label"><span
                            class="text-danger">∗</span> 银行卡号:</label>
                    <div class="col-md-4 input-group" style="padding: 8px 10px 0px 0px;">
                        <?php echo $model['cardNo'] ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                            class="text-danger">∗</span> 图片:</label>
                    <div class="col-md-4 view" style="padding: 0px 10px 0px 0px;">
                        <img style="width:50px;height:50px;" src="<?php echo $model['image']['identity_front_images'] ?>">
                        <img style="width:50px;height:50px;" src="<?php echo $model['image']['identity_back_images'] ?>">
                        <img style="width:50px;height:50px;" src="<?php echo $model['image']['identity_personal_images'] ?>">
                        <img style="width:50px;height:50px;" src="<?php echo $model['image']['hold_identity_images'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 text-right control-label"><span
                            class="text-danger">∗</span> 审核状态:</label>
                    <div class="col-md-4" style="padding: 8px 10px 0px 0px;">
                        <?php if($model['status'] != 1){
                            if($model['status'] == 2){
                                echo '未通过';
                            }else{
                                echo '通过';
                            }
                        }else{
                            ?>
                        <select name="status" id="status" class="form-control">
                            <option value="2">未通过</option>
                            <option value="3">通过</option>
                        </select>
                        <?php } ?>
                    </div>

                </div>


                <div class="form-group">
                    <label class="col-md-1 text-right control-label"></label>
                    <div class="col-md-4" style="padding: 0px 10px 0px 0px;">
                    </div>
                    <?php if($model['status'] == 1){ ?>
                    <div class="col-md-4">
                        <button id="submit-btn" type="button" class="btn btn-primary">提&nbsp;&nbsp;交</button>
                        &nbsp;&nbsp;
                        <a href="<?php echo Url::toRoute(['index']); ?>" class="btn btn-default">取&nbsp;&nbsp;消</a>
                    </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imgsrc" tabindex="-1" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
<!--                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->

            </div>
            <div class="modal-body">

                <img style="width:100%;" src="" />
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal -->
</div>

<script>


    $('.date').datetimepicker({
        language: 'zh-CN',
        minView: 4,
        autoclose: true,
        format : 'yyyy-mm-dd'
    });

    $('#submit-btn').click(function(){
        var status = $('#status option:selected').val();
        $.ajax({
            'type' : 'POST',
            'url' : '<?php echo Url::toRoute(["handle"]) ?>',
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$('[name="id"]').val(),'status':status},
            dataType:'json',
            success:function(){

            }
        })
    })

    $('body').on('click','.view img' ,function () {
        // console.log($(this))
        $('#imgsrc').modal();
        $('#imgsrc img').attr('src',$(this).attr('src'));
    });
</script>