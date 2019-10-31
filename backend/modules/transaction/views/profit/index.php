<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\Profit;
$this->title = '代理商收益记录';
$this->params['breadcrumbs'][] = '代理商收益记录';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>

<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<div class="row">
    <div class="col-sm-12">
        <!-- 具体内容 -->
        <div class="box">
            <div class="panel-body">
                <form id="myForm" onsubmit="return bootstrapTable($(this))" action="" method="get" class="form-horizontal">
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1" for="unique_order">订单号</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="unique_order" value="" id="unique_order">
                        </div>
                        <label class="control-label col-sm-1" for="merchantId">商户编号</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="merchantId" value="" id="merchantId">
                        </div>
                        <label class="control-label col-sm-1" for="type">收益类型</label>
                        <div class="col-sm-2">
                            <?php echo html::dropDownList('type','',[''=>'全部']+Profit::typeLabels(),['class'=>'form-control']) ?>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1" for="serialNo">机具编号</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="serialNo" value="" id="serialNo">
                        </div>
                        <label class="control-label col-sm-1" for="user_code">代理商编号</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="user_code" value="" id="user_code">
                        </div>
                        <label class="control-label col-sm-1" for="real_name">代理商名称</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="real_name" value="" id="real_name">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1">收益日期</label>
                        <div class="col-sm-2">
                            <div class='input-group date'>
                                <input placeholder="请输入发送日期" value="" name="created_at_start"  type='date' class="form-control" />
                                <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class='input-group date' id="end">
                                <input placeholder="请输入发送日期" value="" name="created_at_end"  type='date' class="form-control" />
                                <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                            </div>
                        </div>
                        <label class="control-label col-sm-1" for="user_code">所属服务商</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="agent_name" value="" id="agent_name">
                        </div>
                        <label class="control-label col-sm-1"></label>
                        <div class="col-sm-3" style="text-align:left;">
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
        <div class="box">
            <div class="panel-body">
                <div id="toolbar" class="btn-group">
                    <button  type="button" onclick="exports()" class="btn btn-default add">
                        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> 导出
                    </button>
                </div>
                <table id="table" class="table table-striped table-bordered bulk_action">

                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
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
</div>
<div class="row">
<!-- 模态框（Modal） -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 50px;">
    </div>
</div>
<script type="text/javascript">
    // $(function () {
    //
    //     //1.初始化Table
    //     var oTable = new TableInit();
    //     oTable.Init();
    //
    // });
    function bootstrapTable(){
            $('#table').bootstrapTable('destroy');
            $('#table').bootstrapTable({
                url: '<?php echo Url::toRoute(['list']) ?>',         //请求后台的URL（*）
                method: 'post',                      //请求方式（*）
                toolbar: '#toolbar',                //工具按钮用哪个容器
                striped: true,                      //是否显示行间隔色
                cache: false,                       //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
                pagination: true,                   //是否显示分页（*）
                sortable: true,                     //是否启用排序
                sortName: 'id', // 要排序的字段
                sortOrder: "asc",                   //排序方式
                sortName: 'created_at', // 要排序的字段
                queryParams: queryParams,//传递参数（*）
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
                    // getSelections();
                },
                onCheckAll:function(rows){
                    // getSelections();
                },
                onUncheck:function(row){
                    //console.log(row);
                    // getSelections();
                },
                onUnCheckAll:function(rows){
                    // getSelections();
                    // alert(111);
                },
                columns: [{
                    checkbox: true
                }, {
                    field: 'unique_order',
                    title: '订单号'
                }, {
                    field: 'merchantId',
                    title: '商户编号'
                }, {
                    field: 'merchantName',
                    title: '商户名称'
                },
                {
                    field:'real_name',
                    title:'代理商名称'
                },
                {
                    field:'user_code',
                    title:'代理商编号'
                },
                {
                    field:'agent_name',
                    title:'所属服务商'
                },
                {
                    field:'serialNo',
                    title:'机具编号'
                },
                {
                    field:'amount',
                    title:'交易金额'
                },
                {
                    field:'type',
                    title:'收益类型'
                },
                {
                    field:'amount_profit',
                    title:'收益金额'
                },
                {
                    field:'created_at',
                    title:'收益时间'
                },
                {
                    formatter: function (value, row, index) {
                        return `<div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:view(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="glyphicon glyphicon-eye-open"></i> 查看详情</font></font></a>
                  </li>
                </ul>`;
                    },
                    align: 'center',
                    valign: 'middle',
                    title: '操作'
                }
                ]
            });
            return false;
        }

        //得到查询的参数
    function queryParams (params) {
        params['<?= \Yii::$app->request->csrfParam?>']=$("[name='csrf-token']").attr('content');
        var f = $('#myForm').serializeArray();
        for(var i in f){
            params[f[i]['name']] = f[i]['value'];
        }
        // console.log(params);
        //console.log($('#myForm').serializeArray());
        return params;
    };
    bootstrapTable();

    $('.date').datetimepicker({
        language: 'zh-CN',
        minView: 4,
        autoclose: true,
        format : 'yyyy-mm-dd'
    }).on('changeDate',function(ev){
    let startTime = $('input[name="created_at_start"]').val()
    $('#end').datetimepicker('setStartDate',startTime)
});

    //    导出
    function exports(){
        var unique_order = $('input[name="unique_order"]').val();
        var user_code = $('input[name="user_code"]').val();
        var type = $('input[name="type"]').val();
        var serialNo = $('input[name="serialNo"]').val();
        var merchantId = $('input[name="merchantId"]').val();
        var real_name = $('input[name="real_name"]').val();
        var created_at_start = $('input[name="created_at_start"]').val();
        var created_at_end = $('input[name="created_at_end"]').val();

        if(type == undefined)
        {
            type = ''
        }

        window.location.href = '<?php echo Url::toRoute(['profit/export']); ?>?unique_order='+unique_order+'&user_code='+user_code+
            '&type='+type+'&serialNo='+serialNo+'&merchantId='+merchantId+'&real_name='+real_name+'&created_at_start='+created_at_start+'&created_at_end='+created_at_end
    }
function view($id){
    if($id==''){
        return false;
    }
    $.ajax({
        type:"GET",
        async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['view']) ?>",
        data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
        dataType:'json',
        success:function(result){
            $('#myModal').html(result['html']);
            $('#myModal').modal();
        }
    });
}

</script>