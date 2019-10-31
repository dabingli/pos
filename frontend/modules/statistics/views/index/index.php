<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;

$this->title = '代理商交易统计';
$this->params['breadcrumbs'][] = '代理商交易统计列表';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
                <div class="panel-body">
                       <div class="panel-body">
                            <form onsubmit="return false" id="myForm" action="" method="get" class="form-horizontal">
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1" for="account">代理商编号</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" name="user_code" value="">
                                    </div>
                                    <label class="control-label col-sm-1" for="account">代理商</label>
									<div class="col-sm-3">
                                        <input type="text" class="form-control" name="real_name" value="">
                                    </div>
                                    <label class="control-label col-sm-1" for="account">手机号</label>
									<div class="col-sm-3">
                                        <input type="text" class="form-control" name="mobile" value="">
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1" for="account">统计日期</label>
                                    <div class="col-sm-3">
                                        <div class='input-group date'>
                                            <input placeholder="请输入注册日期" value="" name="created_start"  type='date' class="form-control" />
                                            <span class="input-group-addon">
                                                       <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class='input-group date' id="end">
                                            <input placeholder="请输入注册日期" value="" name="created_end"  type='date' class="form-control" />
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
                        <button  type="button" class="btn btn-default" onclick="exports(this)">
                            <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span> 导出
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
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	if(checkedbox.length > 0){
		//$('#btn_start').attr("disabled",false);
		//$('#btn_stop').attr("disabled",false); 
		//$('#btn_delete').attr("disabled",false);   
	}else{
		//$('#btn_start').attr("disabled",true);
		//$('#btn_stop').attr("disabled",true); 
		//$('#btn_delete').attr("disabled",true); 
	}
}
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
        pageList: [10, 20],        //可供选择的每页的行数（*）
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
    			//alert(111);
    		},
        columns: [{
            checkbox: true
        },{
            field: 'real_name',
            title: '代理商'
        },
        {
            field: 'user_code',
            title: '代理商编号'
        },
        {
            field: 'mobile',
            title: '手机号'
        },
        {
            field: 'total_money',
            title: '交易金额 <span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="当前代理商直营商户和间推商户的累计交易金额"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        },
        {
            field: 'activate_money',
            title: '返现收益 <span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="机具激活的返现收益合计"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        },
        {
            field: 'profit_money',
            title: '分润收益 <span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="商户交易的分润收益合计"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        },
        {
        	formatter: function (value, row, index) {
        		var money = parseFloat(row['profit_money'])+parseFloat(row['activate_money']);
        		return money.toFixed(2);
            },
            valign: 'middle',
            title: '总收益<span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="当前代理商的返现收益和分润收益加总"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        },
        {
            field: 'son',
            title: '直属代理商 <span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="当前代理商直推的下级代理商数量"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        },
        {
            field: 'sons',
            title: '全部代理商 <span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="当前代理商的所有下级代理商（包含直推和间推）数量加总"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        },
        {
            field: 'merchant',
            title: '直营商户 <span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="当前代理商直推的商户数量"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        },
        {
            field: 'merchants',
            title: '全部商户 <span class="text-muted">  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="当前代理商的所有商户（包含直营和间推）数量加总"><span class="glyphicon glyphicon-info-sign" ></span></a></span>'
        }],
        onLoadSuccess:function(){  
        	$('[data-toggle="tooltip"]').tooltip();
        }
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
};
bootstrapTable();
$('.date').datetimepicker({
    language: 'zh-CN',
    minView: 4,
    autoclose: true,
    format : 'yyyy-mm-dd'
}).on('changeDate',function(ev){
    let startTime = $('input[name="created_start"]').val()
    $('#end').datetimepicker('setStartDate',startTime)
});

function exports(e){

    $(e).attr('disabled', true);
    setTimeout(
        function() {
            $(e).attr('disabled', false)
        },
        10000
    );

    var mobile = $('input[name="mobile"]').val();
    var real_name = $('input[name="real_name"]').val();
    var user_code = $('input[name="user_code"]').val();
    var created_start = $('input[name="created_start"]').val();
    var created_end = $('input[name="created_end"]').val();

    window.location.href = '<?php echo Url::toRoute(['export']); ?>?mobile='+mobile+'&user_code='+user_code+
        '&real_name='+real_name+'&created_start='+created_start+ '&created_end='+created_end;
}
</script>