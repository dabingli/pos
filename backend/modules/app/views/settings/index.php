<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\entities\ProductType;
$this->title = 'APP配置管理';
$this->params['breadcrumbs'][] = 'APP管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                <form id="myForm" action="<?php echo Url::toRoute('settings/index'); ?>" method="get" class="form-horizontal">
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1" for="name">APP名称</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="name" value="<?php echo \Yii::$app->request->get('name'); ?>" id="name">
                        </div>

                    </div>

                    <div class="form-group" style="margin-top:15px">
                        <div class="col-sm-3">
                        </div>
                        <div class="col-sm-4" style="text-align:left;">
                            <button type="submit" class="btn btn-primary">查 询</button>
                            <a href="<?php echo Url::toRoute(['settings/index']); ?>" class="btn btn-success">重 置</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                    <table id="table" class="table table-striped table-bordered bulk_action">
                    </table>
                </div>
            </div>
    </div>
</div>

    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 100px;">

    </div>
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="delcfmOverhaul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <form id="modalFrom" style="display:none">
                        <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                    </form>

                    <button  type="button" class="btn btn-primary" onclick="deleteHaulBtn()">确认</button>
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
                    <form id="startFrom" style="display:none">
                        <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                    </form>
                    <button  type="button" class="btn btn-primary" onclick="startHaulBtn()">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <script type="text/javascript">
        $(function () {

            //1.初始化Table
            var oTable = new TableInit();
            oTable.Init();

        });



        var TableInit = function () {
            var oTableInit = new Object();
            //初始化Table
            oTableInit.Init = function () {
                $('#table').bootstrapTable({
                    url: '<?php echo Url::toRoute(['settings/list']) ?>',         //请求后台的URL（*）
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
                    columns: [{
                        checkbox: true
                    }, {
                        field: 'id',
                        title: 'app_id'
                    }, {
                        field: 'name',
                        title: 'APP名称'
                    }, {
                        field: 'mobile',
                        title: '客服电话'
                    }, {
                        field: 'create_name',
                        title: '创建人'
                    },
                    {
                        field: 'created_at',
                        title: '创建日期'
                    },
                    {
                        field:'update_name',
                        title:'修改人'
                    },
                    {
                        field:'updated_at',
                        title:'修改日期'
                    },
                        {
                            formatter: function (value, row, index) {
                                return '<a style="cursor:pointer" title="修改" class="ml-5" onclick="edit(\'' + row.id + '\')"><i class="fa fa-pencil"></i></a>';
                            },
                            align: 'center',
                            valign: 'middle',
                            title: '操作'
                        }]
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
            loading.show();
            var l = Ladda.create(this);
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['settings/add']) ?>",
                data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content')},
                dataType:'json',
                success:function(result){
                    $('#add').html(result['html']);
                    $('#add').modal();
                },
                beforeSend : function(){
                    l.start();
                    $(this).attr("disabled","true");
                },
                complete : function(){
                    l.stop();
                    $(this).attr("disabled",false);
                    loading.hide();
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
                url:"<?php echo Url::toRoute(['settings/edit']) ?>",
                data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
                dataType:'json',
                success:function(result){
                    $('#add').html(result['html']);
                    $('#add').modal();
                }
            });
        }



</script>