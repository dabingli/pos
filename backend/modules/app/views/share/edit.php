<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/fileinput/js/fileinput.js"></script>
<link rel="stylesheet" href="<?=\yii::$app->request->baseUrl?>/fileinput/css/fileinput.css">
<script src="<?=\yii::$app->request->baseUrl?>/fileinput/js/locales/zh.js"></script>
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
                <span id="modalDetailTitle">修改APP分享图片</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['share/edit-do']); ?>" enctype="multipart/form-data" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="image" value="<?php echo $app_share->image; ?>">
                <input type="hidden" name="id" value="<?php echo $app_share->id; ?>">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>图片说明:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入图片说明" value="<?php echo $app_share->description; ?>" type="text"
                               class="form-control" name="description"
                               aria-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>排序:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input  placeholder="排序" value="<?php echo $app_share->sort; ?>" type="number"
                                class="form-control required"  name="sort"
                                aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">
                        <span class="text-danger">∗</span>
                        图片:
                    </label>
                    <div class="col-md-8">
                        <input class="form-control" id="image" name="file" type="file" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>状态:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <?php echo Html::dropDownList('status',$app_share->status,[''=>'请选择']+$status,['class'=>'form-control required']); ?>

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
                        url: "<?php echo Url::toRoute('share/edit-do'); ?>",     //后台处理程序
                        type: "post",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            'description': $("#form input[name='description']").val() ,
                            'sort' : $("#form input[name='sort']").val(),
                            'status' : $("#form input[name='status']").val(),
                            'id' : $("#form input[name='id']").val(),
                            'image' : $("#form input[name='image']").val(),
                        }
                    }
                }
            },
            onkeyup: false,
            messages: {
                'sort': '排序不能为空',
                'image':'请上传图片',
                'status' : '请选择状态',
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

    filename = function() {
        return $(".file-caption-name").attr('title');
    };

    var btnCust = '';
    $("#image").fileinput({
        language : 'zh',//设置文中文
        uploadUrl : "<?php echo Url::toRoute('/file/images'); ?>",//图片上传的url，我这里对应的是后台struts配置好的的action方法
        showCaption : true,//显示标题
        showRemove : true, //显示移除按钮
        uploadAsync : true,//默认异步上传
        showPreview : true,//是否显示预览
        textEncoding : "UTF-8",//文本编码
        autoReplaceBoolean : false,//选择图片时不清空原图片


        overwriteInitial: true,
        showUpload : false,

        showClose: false,
        enctype:'multipart/form-data',

        browseLabel: '',
        removeLabel: '',
        browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        defaultPreviewContent: '<img src="<?php echo $app_share->image; ?>" width="100%" alt="等级图片">',
        layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif"],
        maxFileSize : 2048,
        uploadExtraData: function(previewId, index) {   //额外参数的关键点
            var obj = {};
            var image = filename();
            obj.image = image;
            obj.drive = '<?php echo Yii::$app->params['uploadConfig']['images']['drive']; ?>'
            return obj;
        }


    }).on("filebatchselected", function(event, files) {
        $(this).fileinput("upload");
    }).on("fileuploaded", function (event, data, previewId, index){
        // console.log(123);
        $('#form input[name="image"]').val(data['response']['data']['url'])
    });
</script>