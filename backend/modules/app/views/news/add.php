<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>



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
                <span id="modalDetailTitle">新增图文消息</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['add-do']); ?>" enctype="multipart/form-data" method="post"
                  novalidate="novalidate">
				<div class="hidden"></div>
                
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>图文标题:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入图文标题" value="" type="text" class="form-control required" name="title" aria-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span class="text-danger">∗</span>图文内容:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <textarea rows="10" cols="30" placeholder="图文内容" class="form-control required" name="content" ></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">图片:
                    </label>
                    <div class="col-md-8">
                        <input  id="image" name="file" type="file" />
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
            messages: {
                'title': '名称不能为空',
                'image':'请上传图片',
                'content' : '内容不能为空',
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

        showClose: false,
        enctype:'multipart/form-data',

        //maxFileCount:1,
        maxFileSize : 2048,
        uploadExtraData: function(previewId, index) {   //额外参数的关键点
            var obj = {};
            var image = filename();
            obj.image = image;
            obj.drive = '<?php echo Yii::$app->params['uploadConfig']['images']['drive']; ?>'
            return obj;
        }

    }).on("fileuploaded", function (event, data, previewId, index){
        // alert(event);
    	
    	$('#form .hidden').append('<input type="hidden" name="image[]" value="'+data['response']['data']['url']+'" />');
        //console.log(data['response']);
        //$('#form input[name="image[]"]').val(data['response']['data']['url'])
    });
</script>