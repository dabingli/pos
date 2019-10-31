<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\helpers\HtmlHelper;
use common\models\user\BankCard;

$this->title = '银行卡管理';
$this->params['breadcrumbs'][] = '平台首页';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<?= HtmlHelper::cssFile('@web/resources/plugins/cropper_v1.5/css/cropper.css'); ?>
<?= HtmlHelper::cssFile('@web/resources/plugins/cropper_v1.5/css/main.css'); ?>

<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>

<link href="<?= \yii::$app->request->baseUrl . "/resources/bootstrap/bootstrap-switch/css/bootstrap2/bootstrapSwitch.css"?>" rel="stylesheet" />

<link href="<?=\yii::$app->request->baseUrl?>/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-editable.min.js"></script>

<div class="row">
    <div class="col-sm-12">
        <!-- 具体内容 -->
        <div class="box">
				<div class="panel-body">
                    <form id="myForm" onsubmit="return bootstrapTable($(this))" action="" method="get" class="form-horizontal">
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1" for="name">银行名称</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="name" value="" id="name">
                            </div>
                           <div class="col-sm-4" style="text-align:left;">
                                <button type="submit" class="btn btn-primary">查 询</button>
                            	<a href="<?php echo Url::toRoute(['index']); ?>" class="btn btn-success">重 置</a>
                            </div>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <!-- 具体内容 -->
        <div class="box">
			<div class="panel-body">
				<div id="toolbar" class="btn-group">
                        <button id="btn_add" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                        </button>
                        <button disabled id="btn_start" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span> 启用
                        </button>
                        <button disabled id="btn_close" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-stop" aria-hidden="true"></span> 禁用
                        </button>
               	 	</div>
                  <table id="table" class="table">
                          
                  </table>
             </div>
        </div>
    </div>
</div>

<!-- 模态框（Modal） 批量修改状态 -->
<div class="modal fade" id="batch-edit-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                <form id="statusForm" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
                <button  type="button" class="btn btn-primary" onclick="statusHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<!-- 模态框（Modal） 新增银行 -->
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 100px;"></div>

<!-- 模态框（Modal） 上传图片 -->
<div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="avatar-form">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal" type="button">&times;</button>
                    <h4 class="modal-title" id="avatar-modal-label">LOGO上传</h4>
                </div>
                <div class="modal-body model-padding">
                    <div class="avatar-body">
                        <div class="avatar-upload">
                            <input class="avatar-src" name="avatar_src" type="hidden">
                            <input class="avatar-data" name="avatar_data" type="hidden">
                            <button class="btn btn-primary"  type="button" style="height: 35px;" onClick="$('input[id=avatarInput]').click();">图片选择</button>
                            <span id="avatar-name" style="display: none"></span>
                            <input class="avatar-input hide" id="avatarInput" name="avatar_file" type="file" accept="image/*">
                        </div>
                        <div class="row padding-rem">
                            <div class="col-md-9">
                                <div class="img-container">
                                    <img src=""/>
                                </div>
                            </div>
                            <div class="docs-preview clearfix">
                                <div class="img-preview preview-lg" id="imageHeadLg"></div>
                                <div class="img-preview preview-md" id="imageHeadMd"></div>
                                <div class="img-preview preview-sm" id="imageHeadSm"></div>
                                <!--                                <div class="img-preview preview-xs" id="imageHeadXs"></div>-->
                            </div>
                        </div>
                        <div class="row padding-rem" id="actions">
                            <div class="docs-buttons">
                                <div class="col-md-3">
                                    <span class="btn btn-white fa fa-undo"  data-toggle="tooltip" data-method="rotate" data-option="-90" title="向左旋转90°"> 左旋转</span>
                                    <span class="btn  btn-white fa fa-repeat"  data-toggle="tooltip" data-method="rotate" data-option="90" title="向右旋转90°"> 右旋转</span>
                                </div>
                                <div class="col-md-6" style="text-align: right;">
                                    <div class="btn btn-white fa fa-arrows" data-toggle="tooltip" data-method="setDragMode" data-option="move" title="移动"> 移动</div>
                                    <div class="btn btn-white fa fa-crop" data-toggle="tooltip" data-method="setDragMode" data-option="crop" title="裁剪"> 裁剪</div>
                                    <div class="btn btn-white fa fa-search-plus" data-toggle="tooltip" data-method="zoom" data-option="0.1" title="放大图片"> 放大</div>
                                    <div class="btn btn-white fa fa-search-minus" data-toggle="tooltip" data-method="zoom" data-option="-0.1" title="缩小图片"> 缩小</div>
                                    <div type="button" class="btn btn-white fa fa-refresh"  data-toggle="tooltip" data-method="reset" title="重置图片"> 重置</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary avatar-save" data-dismiss="modal">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= HtmlHelper::jsFile('@web/resources/plugins/cropper_v1.5/js/google-analytics.js')?>
<?= HtmlHelper::jsFile('@web/resources/plugins/cropper_v1.5/js/cropper.js')?>
<?= HtmlHelper::jsFile('@web/resources/plugins/cropper_v1.5/js/main.js')?>
<?= HtmlHelper::jsFile('@web/resources/plugins/cropper_v1.5/js/html2canvas.min.js')?>

<script type="text/javascript">

    var logo = false;
    var bodyHeight = 0;

    function bootstrapTable(){
        $('#table').bootstrapTable('destroy');
        $('#table').bootstrapTable({
            url: '<?php echo Url::toRoute(['list']) ?>',         //请求后台的URL（*）
            method: 'post',                      //请求方式（*）
            toolbar: '#toolbar',                //工具按钮用哪个容器
            striped: true,                      //是否显示行间隔色
            cache: false,                       //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
            pagination: true,                   //是否显示分页（*）
            sortable: false,                     //是否启用排序
            sortName: 'bank', // 要排序的字段
            sortOrder: "asc",                   //排序方式
            sortName: 'created_at', // 要排序的字段
            queryParams: queryParams,//传递参数（*）
            sidePagination: "server",           //分页方式：client客户端分页，server服务端分页（*）
            pageNumber:1,                       //初始化加载第一页，默认第一页
            pageSize: <?php echo Yii::$app->debris->config('sys_page'); ?>,                       //每页的记录行数（*）
            pageList: [10, 20, 30],        //可供选择的每页的行数（*）
            search: false,                       //是否显示表格搜索，此搜索是客户端搜索，不会进服务端，所以，个人感觉意义不大
            contentType: "application/x-www-form-urlencoded",
            strictSearch: true,
            showColumns: true,                  //是否显示所有的列
            showRefresh: true,                  //是否显示刷新按钮
            minimumCountColumns: 2,             //最少允许的列数
            clickToSelect: true,                //是否启用点击选中行
            uniqueId: "bank",                     //每一行的唯一标识，一般为主键列
            showToggle:false,                    //是否显示详细视图和列表视图的切换按钮
            cardView: false,                    //是否显示详细视图
            detailView: false,                   //是否显示父子表
            selectItemName:'bank[]',
            //height: 600,                        //行高，如果没有设置height属性，表格自动根据记录条数觉得表格高度

            onCheck:function(row){
                //console.log(row);
                getSelections();
            },
            onCheckAll:function(rows){
                getSelections();
            },
            onUncheck:function(row){
                //console.log(row);
                getSelections();
            },
            onUnCheckAll:function(rows){
                getSelections();
                //alert(111);
            },
            columns: [{
                checkbox: true
            }, {
                field: 'bank',
                title: '银行简称'
            },
                {
                    formatter: function (value, row, index) {
                        return `<div class="update" data-name="name" data-pk="`+row['bank']+`">
                `+row['name']+`
            	</div>`;
                    },
                    valign: 'middle',
                    title: '银行卡名称'
                },
                {
                    formatter: function (value, row, index) {
                        return `<img style="float: none;" onclick="return avatar($(this))" data-name="logo" data-pk="`+row['bank']+`" class="img-circle rf-img-md" src="`+row['logo']+`" />`;
                    },
                    valign: 'middle',
                    title: '银行logo'
                }, {
                    formatter: function (value, row, index) {

                        var open = '<?php echo BankCard::OPEND_STATUS; ?>';
                        var status = row.o_status == open ? 'checked' : '';
                        var editStatus = row.o_status == open ? '<?php echo BankCard::CLOSE_STATUS; ?>' : open;

                        return `<div class="switch" data-name="status" data-pk="`+row.bank+`" data-value="`+row.o_status+`"  data-edit-value="`+editStatus+`" data-on="success" data-off="danger">
                                    <input type="checkbox" `+ status +` />
            	                </div>`;
                    },
                    valign: 'middle',
                    title: '状态'
                },
                {
                    formatter: function (value, row, index) {
                        return `<div class="order" data-name="order" data-pk="`+row['bank']+`">
                `+row['order']+`
            	</div>`;
                    },
                    valign: 'middle',
                    title: '排序'
                }],
            onLoadSuccess:function(){
                $(".update").editable({
                    title: '银行卡名称',
                    url: function (params) {
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo Url::toRoute('ajax-edit') ?>",
                            data: {'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'name':params.name,'pk':params.pk,'value':params['value']},
                            dataType: 'JSON',
                            success: function (data, textStatus, jqXHR) {

                            },
                            error: function () {
                                rfError('保存失败啦!');
                            }
                        });
                    },
                    type: 'text'
                });
                $(".order").editable({
                    validate: function (v) {
                        if (isNaN(v)) return '排序必须是数字';
                        var age = parseInt(v);
                        if (age <= 0) return '排序必须是正整数';
                    },
                    title: '排序',
                    url: function (params) {
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo Url::toRoute('ajax-edit') ?>",
                            data: {'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'name':params.name,'pk':params.pk,'value':params['value']},
                            dataType: 'JSON',
                            success: function (data, textStatus, jqXHR) {

                            },
                            error: function () {
                                rfError('保存失败啦!');
                            }
                        });
                    },
                    type: 'text'
                });

                // 引入 bootstrapSwitch.js 文件
                new_element=document.createElement("script");
                new_element.setAttribute("type","text/javascript");
                new_element.setAttribute("src","<?=\yii::$app->request->baseUrl?>/resources/bootstrap/bootstrap-switch/js/bootstrapSwitch.js");
                document.body.appendChild(new_element);

                // 修改 状态
                $('.switch').on('switch-change', function (e, data) {

                    var _this = $(data.el).parent().parent();
                    var pk = _this.data('pk');
                    var name = _this.data('name');
                    var value = _this.data('value');
                    var editValue = _this.data('edit-value');

                    $.ajax({
                        type: 'POST',
                        url: "<?php echo Url::toRoute('ajax-edit') ?>",
                        data: {'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'name':name,'pk':pk,'value':editValue},
                        dataType: 'JSON',
                        success: function (data, textStatus, jqXHR) {
                            _this.data('value', editValue);
                            _this.data('edit-value', value);
                        },
                        error: function () {
                            rfError('保存失败啦!');
                        }
                    });
                });
            }
        });
        return false;
    }

    function getSelections(){
        checkedbox= $("#table").bootstrapTable('getSelections');
        if(checkedbox.length > 0){
            $('#btn_start').attr("disabled",false);
            $('#btn_close').attr("disabled",false);
        }else{
            $('#btn_start').attr("disabled",true);
            $('#btn_close').attr("disabled",true);
        }
    }
    //得到查询的参数
    function queryParams (params) {
        params['<?= \Yii::$app->request->csrfParam?>']=$("[name='csrf-token']").attr('content');
        var f = $('#myForm').serializeArray();
        for(var i in f){
            params[f[i]['name']] = f[i]['value'];
        }
        //console.log(params);
        //console.log($('#myForm').serializeArray());
        return params;
    };
    bootstrapTable();


    $("#btn_add").click(function(){

        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['add']) ?>",
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content')},
            dataType:'json',
            success:function(result){
                $('#add').html(result['html']);
                $('#add').modal();
            },
            beforeSend : function(){
                $(this).attr("disabled","true");
            },
            complete : function(){
                $(this).attr("disabled",false);
            }
        });
    });

    $('#btn_start').click(function (){

        editStatusModel('<?php echo BankCard::OPEND_STATUS; ?>', '你确定启用已选择的银行信息吗');

    });

    $('#btn_close').click(function (){

        editStatusModel('<?php echo BankCard::CLOSE_STATUS; ?>', '你确定禁用已选择的银行信息吗');

    });

    function editStatusModel(status, msg){
        checkedbox= $("#table").bootstrapTable('getSelections');
        $("#startForm input[name='pk[]']").remove();

        $("#statusForm").append('<input type="hidden" name="status" value="'+status+'" />');
        $("#batch-edit-info").find('.modal-dialog').find('.modal-content').find('.modal-body').text(msg);

        $ids = new Array();
        for(var i in checkedbox){
            $ids.push(checkedbox[i]['bank']);
            $("#statusForm").append('<input type="hidden" name="pk[]" value="'+checkedbox[i]['bank']+'" />');
        }
        if($ids.length<=0){
            return false;
        }
        $("#batch-edit-info").modal();
    }

    function statusHaulBtn(){
        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['ajax-edit-status']) ?>",
            data:$("#statusForm").serialize(),
            dataType:'json',
            success:function(result){
                location.reload();
            }
        });
    }


    function avatar(e){
        logo = e;
        //记录当前滚动条位置
        bodyHeight = $("html").scrollTop();
        //让滚动条置顶
        scrollHeight(0);
        $('#avatar-modal').modal();
    }

    function scrollHeight(height){
        $("html").scrollTop(height);
    }

    // 做个下简易的验证  大小 格式 保留
    $('#avatarInput').on('change', function(e) {
        return false;
        var filemaxsize = 1024 * 5;// 5M
        var target = $(e.target);
        if(!target[0].files[0]){
            return false;
        }
        var Size = target[0].files[0].size / 1024;
        console.log(Size);
        if(Size > filemaxsize) {
            rfError('图片过大，请重新选择!');
            $(".avatar-wrapper").children().remove;
            return false;
        }

        if(!this.files[0].type.match(/image.*/)) {
            rfError('请选择正确的图片!');
            return false;
        } else {
            var filename = document.querySelector("#avatar-name");

            var texts = document.querySelector("#avatarInput").value;
            console.log(texts);

            var teststr = texts; // 你这里的路径写错了
            testend = teststr.match(/[^\\]+\.[^\(]+/i); // 直接完整文件名的
            filename.innerHTML = testend;
        }
    });

    $(".avatar-save").on("click", function() {
        // 截图小的显示框内的内容
        var targetDom = $("#imageHeadLg");
        var copyDom = targetDom.clone();
        copyDom.attr('id', 'copyDom');
        copyDom.width(targetDom.width() + "px");
        copyDom.height(targetDom.height() + "px");
        copyDom.css({'z-index':"-1",'position':'absolute',"bottom": "0px",'left':'0px',"background-color": "white"});
        $('body').append(copyDom);
        html2canvas(document.querySelector("#copyDom"),{
            allowTaint: true
        }).then(canvas => {
            // 恢复滚动条位置
            scrollHeight(bodyHeight);
            var dataUrl = canvas.toDataURL();
            var base64 = dataUrl.split(',');
            copyDom.remove();
            imagesAjax(base64[1]);
        });
    });

    function imagesAjax(src) {
        var data = {};
        data.image = src;
        data.jid = $('#jid').val();
        data.drive = '<?php echo Yii::$app->params['uploadConfig']['images']['drive']; ?>';
        $.ajax({
            url : "<?= Url::to(['/file/base64'])?>",
            type : "post",
            dataType : 'json',
            data : data,
            success : function(data) {
                if(data.code == 200) {
                    url = data.data.url;

                    //新增银行上传图片
                    if(logo === false){
                        $('#bank-img-add').attr('src', url);
                        $('input[name=logo]').val(url);

                        //修改银行图片
                    }else{
                        logo.attr('src',url);
                        saveLogo(url);
                    }

                }else{
                    rfError(data.message)
                }
            }
        });
    }

    function saveLogo(src){

        var pk = logo.data('pk');
        var name = logo.data('name');

        logo = false;

        $.ajax({
            type: 'POST',
            url: "<?php echo Url::toRoute('ajax-edit') ?>",
            data: {'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'name':name,'pk':pk,'value':src},
            dataType: 'JSON',
            success: function (data, textStatus, jqXHR) {

            },
            error: function () {
                rfError('保存失败啦!');
            }
        });
    }

</script>
