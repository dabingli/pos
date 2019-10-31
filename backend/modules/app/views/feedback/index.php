<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\entities\ProductType;
$this->title = 'APP问题反馈';
$this->params['breadcrumbs'][] = 'APP管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                <div id="toolbar" class="btn-group">
                    <button onclick="exports()" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-download-alt"></span>
                         导出
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
            $('#btn_excel').attr("disabled",false);
        }else{
            $('#btn_excel').attr("disabled",true);
        }
    }

    function exports(){
        //$.ajax({
        //    type:"POST",
        //    async:false,//false时为同步true为异步一般是异步
        //    url:"<?php //echo Url::toRoute(['feedback/export']) ?>//",
        //    data:$("#export").serialize(),
        //    dataType:'json',
        //    success:function(result){
        //        location.reload();
        //    }
        //});
        window.location.href = '<?php echo Url::toRoute(['feedback/export']); ?>'
    }


    var TableInit = function () {
        var oTableInit = new Object();
        //初始化Table
        oTableInit.Init = function () {
            $('#table').bootstrapTable({
                url: '<?php echo Url::toRoute(['feedback/list']) ?>',         //请求后台的URL（*）
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
                    field: 'app_name',
                    title: 'APP名称'
                }, {
                    field: 'type',
                    title: '问题类型'
                }, {
                    field: 'description',
                    title: '问题描述'
                },
                    {
                        field:'name',
                        title:'反馈人'
                    },
                    {
                        field:'created_at',
                        title:'反馈时间'
                    },
                    // {
                    //     formatter: function (value, row, index) {
                    //         return '<a style="cursor:pointer" title="修改" class="ml-5" onclick="edit(\'' + row.id + '\')"><i class="fa fa-pencil"></i></a>';
                    //     },
                    //     align: 'center',
                    //     valign: 'middle',
                    //     title: '操作'
                    // }
                    ]
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

</script>