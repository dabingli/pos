<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\HtmlHelper;
use backend\assets\AppAsset;
use common\models\app\AppShare;
$this->title = 'APP分享图片';
$this->params['breadcrumbs'][] = 'APP管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>

<link href="<?= \yii::$app->request->baseUrl . "/resources/bootstrap/bootstrap-switch/css/bootstrap2/bootstrapSwitch.css"?>" rel="stylesheet" />


<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                <div id="toolbar" class="btn-group">
                    <button  type="button" id="btn_add" class="btn btn-default add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                    </button>
                    <button disabled="disabled" id="btn_start" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-star" aria-hidden="true"></span> 启用
                    </button>
                    <button disabled="disabled" id="btn_stop" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-stop" aria-hidden="true"></span> 停用
                    </button>
                </div>
                <table id="table" class="table table-striped table-bordered bulk_action">

                </table>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 100px;">

    </div>
    <!-- 模态框（Modal） -->
<div class="modal fade" id="stopOverhaul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定需要停用吗？
            </div>
            <div class="modal-footer">
                <form id="stopForm" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>

                <button  type="button" class="btn btn-primary" onclick="stopHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="startOverhaul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定需要启用吗？
            </div>
            <div class="modal-footer">
                <form id="startForm" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
                <button  type="button" class="btn btn-primary" onclick="startHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="imgsrc" tabindex="-1" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            </div>
            <div class="modal-body">

                <img style="width:100%;" src="" />
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal -->
</div>
<script type="text/javascript">

    $(function () {

            //1.初始化Table
            var oTable = new TableInit();
            oTable.Init();

        });

        function getSelections(){
            checkedbox= $("#table").bootstrapTable('getSelections');
            // console.log(checkedbox);
            if(checkedbox.length > 0){
                $('#btn_start').attr("disabled",false);
                $('#btn_stop').attr("disabled",false);
                $('#btn_delete').attr("disabled",false);
            }else{
                $('#btn_start').attr("disabled",true);
                $('#btn_stop').attr("disabled",true);
                $('#btn_delete').attr("disabled",true);
            }
        }

        $('#btn_start').click(function (){
            checkedbox= $("#table").bootstrapTable('getSelections');
            // console.log(checkedbox);
            $ids = new Array();
            $("#startForm input[name='id[]']").remove();
            for(var i in checkedbox){
                $ids.push(checkedbox[i]['id']);
                $("#startForm").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
            }
            if($ids.length<=0){
                return false;
            }
            $("#startOverhaul").modal();
        });

        function startHaulBtn(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['share/start']) ?>",
                data:$("#startForm").serialize(),
                dataType:'json',
                success:function(result){
                    location.reload();
                }
            });
        }

        $('#btn_stop').click(function (){

            checkedbox= $("#table").bootstrapTable('getSelections');
            // console.log(checkedbox);
            $ids = new Array();
            $("#stopForm input[name='id[]']").remove();
            for(var i in checkedbox){
                $ids.push(checkedbox[i]['id']);
                $("#stopForm").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
            }
            if($ids.length<=0){
                return false;
            }
            $("#stopOverhaul").modal({
                backdrop : 'static',
                keyboard : false
            });
        });
        function stopHaulBtn(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['share/stop']) ?>",
                data:$("#stopForm").serialize(),
                dataType:'json',
                success:function(result){
                    location.reload();
                }
            });
        }


        var TableInit = function () {
            var oTableInit = new Object();
            //初始化Table
            oTableInit.Init = function () {
                $('#table').bootstrapTable({
                    url: '<?php echo Url::toRoute(['share/list']) ?>',         //请求后台的URL（*）
                    method: 'post',                      //请求方式（*）
                    toolbar: '#toolbar',                //工具按钮用哪个容器
                    striped: true,                      //是否显示行间隔色
                    cache: false,                       //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
                    pagination: true,                   //是否显示分页（*）
                    sortable: true,                     //是否启用排序
                    sortName: 'id', // 要排序的字段
                    sortOrder: "asc",                   //排序方式
                    sortName: 'created_at', // 要排序的字段
                    queryParams: oTableInit.queryParams,//传递参数（*）
                    sidePagination: "server",           //分页方式：client客户端分页，server服务端分页（*）
                    pageNumber:1,                       //初始化加载第一页，默认第一页
                    pageSize: 10,                       //每页的记录行数（*）
                    pageList: [10, 25, 50, 100],        //可供选择的每页的行数（*）
                    search: false,                       //是否显示表格搜索，此搜索是客户端搜索，不会进服务端，所以，个人感觉意义不大
                    contentType: "application/x-www-form-urlencoded",
                    strictSearch: true,
                    showColumns: true,                  //是否显示所有的列
                    showRefresh: true,                  //是否显示刷新按钮
                    minimumCountColumns: 2,             //最少允许的列数
                    clickToSelect: true,                //是否启用点击选中行
                    uniqueId: "id",                     //每一行的唯一标识，一般为主键列
                    showToggle:false,                    //是否显示详细视图和列表视图的切换按钮
                    cardView: false,                    //是否显示详细视图
                    detailView: false,                   //是否显示父子表
                    selectItemName:'id[]',
                    //height: 600,                        //行高，如果没有设置height属性，表格自动根据记录条数觉得表格高度
                    onLoadError: function () {
                        showTips("数据加载失败！");
                    },
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
                        alert(111);
                    },
                    columns: [{
                        checkbox: true
                    }, {
                        field: 'name',
                        title: 'APP名称'
                    }, {
                        field: 'description',
                        title: '图片说明'
                    }, {
                        field: 'sort',
                        title: '排序'
                    },
                        {
                            field: 'image',
                            title: '图片',
                            formatter:function(value,row,index){
                                var s;
                                if(row.image!=null){
                                    var url = row.image;
                                    s = '<a class = "view src"  href="javascript:void(0)"><img style="width:30px;height:40px;"  src="'+url+'" /></a>';
                                }
                                return s;

                            }
                        },
                        {
                            formatter: function (value, row, index) {
                                var open = '<?php echo AppShare::START; ?>';
                                var status = row.o_status == open ? 'checked' : '';
                                var editStatus = row.o_status == open ? '<?php echo AppShare::STOP; ?>' : open;

                                return `<div class="switch" data-name="status" data-pk="`+row.id+`" data-value="`+row.status+`"  data-edit-value="`+editStatus+`" data-on="success" data-off="danger" data-on-label="启用" data-off-label="禁用">
                                    <input type="checkbox" `+ status +`  />
            	                </div>`;
                            },
                            valign:'middle',
                            title:'状态'
                        },
                        {
                            field:'add_name',
                            title:'添加人'
                        },
                        {
                            field:'created_at',
                            title:'添加时间'
                        },
                        {
                            formatter: function (value, row, index) {
                                return '<a style="cursor:pointer" title="修改" class="ml-5" onclick="edit(\'' + row.id + '\')"><i class="fa fa-pencil"></i></a>';
                            },
                            align: 'center',
                            valign: 'middle',
                            title: '操作'
                        }],
                    onLoadSuccess:function(){
                        // 引入 bootstrapSwitch.js 文件
                        new_element = document.createElement("script");
                        new_element.setAttribute("type", "text/javascript");
                        new_element.setAttribute("src", "<?=\yii::$app->request->baseUrl?>/resources/bootstrap/bootstrap-switch/js/bootstrapSwitch.js");
                        document.body.appendChild(new_element);

                        //修改状态
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
                                    if(data.status == 1){
                                        rfSuccess(data.msg)
                                    }else{
                                        rfError(data.msg);
                                    }

                                },
                                error: function () {
                                    rfError('保存失败啦!');
                                }
                            });
                        });
                    }
                });
            };

            //得到查询的参数
            oTableInit.queryParams = function (params) {
                params.name=$("#name").val();
                params['<?= \Yii::$app->request->csrfParam?>']=$("[name='csrf-token']").attr('content');
                return params;
            };
            return oTableInit;
        };

        $('#btn_add').click(function(){
            // loading.show();
            // var l = Ladda.create(this);
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['share/add']) ?>",
                data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content')},
                dataType:'json',
                success:function(result){
                    $('#add').html(result['html']);
                    $('#add').modal();
                },
                beforeSend : function(){
                    // l .start();
                    $(this).attr("disabled","true");
                },
                complete : function(){
                    // l.stop();
                    $(this).attr("disabled",false);
                    // loading.hide();
                }
            });
        })

        function edit($id){
            if($id==''){
                return false;
            }
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['share/edit']) ?>",
                data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
                dataType:'json',
                success:function(result){
                    $('#add').html(result['html']);
                    $('#add').modal();
                }
            });
        }

        $('body').on('click','.view img' ,function () {
            // console.log($(this))
            $('#imgsrc').modal();
            $('#imgsrc img').attr('src',$(this).attr('src'));
        });

    </script>