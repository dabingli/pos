<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\user\SmsCode;
$this->title = '短信管理';
$this->params['breadcrumbs'][] = '短信发送记录';
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
                        <label class="control-label col-sm-1" for="mobile">接收手机号</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="mobile" value="" id="mobile">
                        </div>
                        <label class="control-label col-sm-1" for="type">短信类型</label>
                        <div class="col-sm-2">
                            <?php echo html::dropDownList('type','',[''=>'全部']+SmsCode::typeLabels(),['class'=>'form-control']) ?>
                        </div>
                        <label class="control-label col-sm-1">发送时间</label>
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

                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1" for="status">状态</label>
                        <div class="col-sm-3">
                            <?php echo html::dropDownList('status','',[''=>'全部']+SmsCode::statusLabels(),['class'=>'form-control']) ?>
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
                    field: 'code',
                    title: '短信编号'
                }, {
                    field: 'mobile',
                    title: '接收手机号'
                }, {
                    field: 'content',
                    title: '短信内容'
                },
                    {
                        field:'type',
                        title:'短信类型'
                    },
                    {
                        field:'created_at',
                        title:'发送时间'
                    },
                    {
                        field:'status',
                        title:'发送状态'
                    },
                    {
                        field:'return_data',
                        title:'备注'
                    },
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
        var mobile = $('#mobile').val();
        var type = $('#type').val();
        var status = $('input[name="status"]').val();
        var created_at_start = $('input[name="created_at_start"]').val();
        var created_at_end = $('input[name="created_at_end"]').val();

        if(type == undefined)
        {
            type = ''
        }
        if(status == undefined)
        {
            status = ''
        }

        // console.log($('select[name="status"]').val())
        window.location.href = '<?php echo Url::toRoute(['sms/export']); ?>?mobile='+mobile+'&type='+type+
            '&status='+status+'&created_at_start='+created_at_start+'&created_at_end='+created_at_end
    }

</script>