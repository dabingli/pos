<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\entities\CashOrder;
$this->title = '代理商信息';
$this->params['breadcrumbs'][] = '代理商信息';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/select2/dist/css/select2.min.css"?>" rel="stylesheet" />
<link href="<?= \yii::$app->request->baseUrl . "/dist/bootstrap-table.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/dist/bootstrap-table.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/select2/dist/js/select2.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<div class="row">
<div class="col-sm-12">
    <div class="box">
                <div class="panel-body">
                            <form onsubmit="return false" id="myForm" action="" method="get" class="form-horizontal">
                                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1">商户编号</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="merchantId" value="">
                                    </div>
                                    <label class="control-label col-sm-1">商户名称</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="merchantName" value="">
                                    </div>
                                    <!--<label class="control-label col-sm-1">订单号</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="orderNo" value="">
                                    </div> -->
                                </div>
                                <div class="form-group" style="margin-top:15px">

                                    <label class="control-label col-sm-1">代理商编号</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="user_code" value="">
                                        <span style="color:red;">*查询下级需要填写完整</span>
                                    </div>
                                    <label class="control-label col-sm-1">代理商</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="real_name" value="">
                                        <span style="color:red;">*查询下级需要填写完整</span>
                                    </div>
                                    <label class="control-label col-sm-1">是否查询下级</label>
                                    <div class="col-sm-2">
                                        <select name="is_search_children" class="form-control">
                                            <option value="1">否</option>
                                            <option value="2">是</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1">机具编号</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" name="serialNo" value="">
                                    </div>

                                    <label class="control-label col-sm-1">激活时间</label>
                                    <div class="col-sm-2">
                                    	<div class='input-group date' id="start">
                                            <input  value="" name="bindingTime_start"  type='date' class="form-control" />
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                              			</div>
                                    </div>
                                    <div class="col-sm-2">
                                    	<div class='input-group date' id="end">
                                            <input  value="" name="bindingTime_end"  type='date' class="form-control" />
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                              			</div>
                                    </div>
                                    <div class="col-sm-3">
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
                        <button  type="button" class="btn btn-default download" id="export">
                            <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span> 导 出
                        </button>
               	 	</div>
					<table id="table" class="table table-striped table-bordered bulk_action">
                      
                    </table>
                  </div>
                </div>
              </div> 
              </div>
              
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 200px;">
    
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
        //sortName: 'id', // 要排序的字段
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
        },
        {
            field: 'merchantId',
            title: '商户编号'
        }, 
        {
            field: 'merchantName',
            title: '商户名称'
        }, 
        {
            field: 'phone',
            title: '商户手机号'
        }, 
        {
            field: 'serialNo',
            title: '机具编号'
        }, 
        {
            field: 'agent_name',
            title: '所属代理商'
        },
        {
            field: 'agent_number',
            title: '代理商编号'
        },{
            field: 'bindingTime',
            title: '激活时间'
        },
        ]
    });
}
//得到查询的参数
function queryParams (params) {
	var str = '';
	for(var i in params){
		str+=i+"="+params[i]+'&';
	}
	var f = $('#myForm').serialize();
	str += f;
    return str;
};
bootstrapTable();

$('.date').datetimepicker({
	language: 'zh-CN',
    minView: 4,
    autoclose: true,
    format : 'yyyy-mm-dd'
}).on('changeDate',function(ev){
    let startTime = $('input[name="bindingTime_start"]').val()
    $('#end').datetimepicker('setStartDate',startTime)
})


$('#export').click(function(){
    var merchantId = $('input[name="merchantId"]').val();
    var merchantName = $('input[name="merchantName"]').val();
    var orderNo = '';//$('input[name="orderNo"]').val();
    var serialNo = $('input[name="serialNo"]').val();
    var user_code = $('input[name="user_code"]').val();
    var real_name = $('input[name="real_name"]').val();
    var bindingTime_start = $('input[name="bindingTime_start"]').val();
    var bindingTime_end = $('input[name="bindingTime_end"]').val();
    var is_search_children = $('select[name="is_search_children"]').val()

    if(is_search_children == null)
    {
        is_search_children = 1;
    }

    window.location.href = '<?php echo Url::toRoute(['merchant/export']); ?>?merchantId='+merchantId+'&merchantName='+merchantName+
            '&orderNo='+orderNo+'&serialNo='+serialNo+'&user_code='+user_code+'&real_name='+real_name+'&bindingTime_start='+bindingTime_start+
            '&bindingTime_end='+bindingTime_end + '&is_search_children='+is_search_children
})
</script>