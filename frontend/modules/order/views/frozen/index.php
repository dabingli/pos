<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\user\User;
use frontend\assets\AppAsset;
use common\models\agent\AgentRechargeLog;
$this->title = '代理商冻结款记录';
$this->params['breadcrumbs'][] = '代理商冻结款管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                    <form onsubmit="return false" id="myForm"  class="form-horizontal">
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">代理商名称</label>
                            <div class="col-sm-2">
                                <input placeholder="请输入代理商名称" type="text" class="form-control" name="name" value="">
                            </div>

                            <label class="control-label col-sm-1">代理商编号</label>
                            <div class="col-sm-2">
                                <input placeholder="请输入代理商编号" type="text" class="form-control" name="number" value="">
                            </div>

                            <label class="control-label col-sm-1">到期日期</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请选择到期日期" value="" name="expire_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请选择到期日期" value="" name="expire_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:15px">

                            <label class="control-label col-sm-1">冻结日期</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请选择冻结日期" value="" name="frozen_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请选择冻结日期" value="" name="frozen_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>

                            <div class="col-sm-3" style="text-align:left;">
                                <button onclick="bootstrapTable()" type="submit" class="btn btn-primary">查 询</button>
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
                    <button onclick="exports()" type="button" class="btn btn-default">
                        导出
                    </button>
                </div>
                <table id="table" class="table table-striped table-bordered bulk_action">

                </table>
            </div>
        </div>
    </div>
</div>


<script>

    function getSelections(){

        return ;

        checkedbox= $("#table").bootstrapTable('getSelections');
        if(checkedbox.length > 0){
            $('#btn_del').attr("disabled",false);

        }else{
            $('#btn_del').attr("disabled",true);
        }
    }

    function bootstrapTable(){
        var $table = $("#table");
        $('#table').bootstrapTable('destroy');
        $('#table').bootstrapTable({
            url: '<?php echo Url::toRoute(['list']) ?>',         //请求后台的URL（*）
            method: 'get',                      //请求方式（*）
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
            //height: 500,                        //行高，如果没有设置height属性，表格自动根据记录条数觉得表格高度
            onLoadSuccess: function(data) {
                $('.fixed-table-pagination').prepend('<span style="margin-left:20px">总冻结金额：'+data.totalMoney+'</span>');
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
                //alert(111);
            },
            columns: [{
                checkbox:true
            },{
                field: 'real_name',
                title: '代理商',
                align: 'center',
                width:200
            },
            {
                field: 'user_code',
                title: '代理商编号',
                align: 'center',
                width:200
            },
            {
                field: 'mobile',
                title: '手机号码',
                align: 'center',
                width:200
            },
            {
                field: 'product_no',
                title: '机具编号',
                align: 'center',
                width:200
            },
            {
                field: 'type_name',
                title: '机具类型',
                align: 'center',
                width:200
            },
            {
                field: 'expire_at',
                title: '到期日期',
                align: 'center',
                width:200
            },
            {
                field: 'frozen_money',
                title: '冻结金额',
                align: 'center',
                width: 200
            },
            {
                field: 'created_at',
                title: '冻结时间',
                align: 'center',
                width: 200
            }],
        });
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
    }

    bootstrapTable();

    $('.date').datetimepicker({
        language: 'zh-CN',
        minView: 4,
        autoclose: true,
        format : 'yyyy-mm-dd'
    });

    //代理商信息导出
    function exports(){
        var name = $('input[name="name"]').val() ? $('input[name="name"]').val() : '';
        var number = $('select[name="number"]').val() ? $('input[name="number"]').val() : '';
        var frozen_start = $('input[name="frozen_start"]').val() ? $('input[name="frozen_start"]').val() : '';
        var frozen_end = $('input[name="frozen_end"]').val() ? $('input[name="frozen_end"]').val() : '';
        var expire_start = $('select[name="expire_start"]').val() ? $('input[name="expire_start"]').val() : '';
        var expire_end = $('select[name="expire_end"]').val() ? $('input[name="expire_end"]').val() : '';
        window.location.href = '<?php echo Url::toRoute(['export']); ?>?name='+name+'&number='+number+'&frozen_start='+frozen_start+'&frozen_end='+frozen_end+'&expire_start='+expire_start+'&expire_end='+expire_end;
    }

</script>