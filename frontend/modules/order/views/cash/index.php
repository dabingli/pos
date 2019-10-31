<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\CashOrder;
$this->title = '交易信息';
$this->params['breadcrumbs'][] = '提现记录';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/select2/dist/css/select2.min.css"?>" rel="stylesheet" />
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
                                    <label class="control-label col-sm-1">订单号</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="cash_order" value="">
                                    </div>
                                    <label class="control-label col-sm-1">代理商</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="user_name" value="">
                                    </div>
                                    <label class="control-label col-sm-1">提现类型</label>
                                    <div class="col-sm-3">
                                    <?php echo Html::dropDownList('type[]','',CashOrder::typeLabels(),['class'=>'form-control','id'=>'sel_menu2','multiple'=>'multiple']); ?>
                                    </div> 
                                </div>
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1">收益日期</label>
                                    <div class="col-sm-2">
                                    	<div class='input-group date'>
                                            <input  value="" name="created_start" id="start"  type='date' class="form-control" />
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                              			</div>
                                    </div>
                                    <div class="col-sm-2">
                                    	<div class='input-group date' id="end">
                                            <input  value="" name="created_end"  type='date' class="form-control" />
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                              			</div>
                                    </div>
                                    <label class="control-label col-sm-1">交易状态</label>
                                    <div class="col-sm-2">
                                    	<?php echo Html::dropDownList('status','',[''=>'全部']+CashOrder::statusLabels(),['class'=>'form-control']); ?>
                                    </div> 
                                    <label class="control-label col-sm-1">代理商编号</label>
                                    <div class="col-sm-3">
                                    	<input type="text" class="form-control" name="user_code" value="">
                                    </div>
                                </div>
                                
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1">审核状态</label>
                                    <div class="col-sm-2">
                                        <?php echo Html::dropDownList('handle','',[''=>'全部']+CashOrder::handleLabels(),['class'=>'form-control']); ?>
                                    </div>

                                    <label class="control-label col-sm-1"></label>
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
                       <button  type="button" class="btn btn-success" id="handle-pass">
                           <span class="glyphicon glyphicon-check" aria-hidden="true"></span> 审核通过
                       </button>
                       <button  type="button" class="btn btn-danger" id="handle-fail">
                       <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> 审核不通过
                       </button>
                        <button  type="button" class="btn btn-default download" onclick="exports()">
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
<div class="modal fade" id="modal-handle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body handle-msg">

            </div>
            <div class="modal-footer">
                <button  type="button" class="btn btn-primary" onclick="handle(this)">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
            <form id="startForm" style="display:none">
                <input type="hidden" name="handle" value="">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script>

function getSelections(){
	checkedbox= $("#table").bootstrapTable('getSelections');
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
        method: 'POST',                      //请求方式（*）
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
        columns: [
            {
                field: 'checkStatus',  //给多选框赋一个field值为“checkStatus”用于更改选择状态!!!!
                valign : 'middle',
                checkbox:true,
                formatter: function (value, row, index) {
                    if(row.handle == '<?php echo CashOrder::AUDIT_WAIT; ?>'){
                        return {
                            disabled: false,
                        }
                    }else{
                        return {
                            disabled: true,
                        }
                    }

                }
            },
            {
                field: 'id',
                align: 'center',
                sortable: true,
                title: 'ID',
                footerFormatter: function (){
                    return '汇总'
                }
            },
            {
                field: 'order',
                title: '订单号'
            },
            {
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
                field: 'type',
                title: '提现类型'
            },
            {
                field: 'cash_amount',
                title: '提现金额'
            },
            {
                field: 'fee',
                title: '手续费'
            },
            {
                field: 'agent_fee',
                title: '平台手续费'
            },
            {
                field: 'account_amount',
                title: '到账金额'
            },
            {
                field: 'account',
                title: '结算账号'
            },
            {
                field: 'cash_provider',
                title: '提现人'
            },
            {
                field: 'created_at',
                title: '提现时间'
            },
            {
                field: 'status',
                title: '交易状态'
            },
            {
                field: 'remarks',
                title: '交易失败原因'
            },
            {
                field: 'handleStr',
                title: '审核状态'
            },
        ]
    });
}
//得到查询的参数
function queryParams (params) {
	//params['<?//= \Yii::$app->request->csrfParam?>//']=$("[name='csrf-token']").attr('content');
	var str = '';
	for(var i in params){
		str+=i+"="+params[i]+'&';
	}
	var f = $('#myForm').serialize();
	str += f;
	//alert(str);
	
	//console.log(JSON.stringify(params));
	//alert(f);
	//return false;
	//for(var i in f){
	//	params[f[i]['name']] = f[i]['value'];
	//}
	// console.log(params);
	//console.log($('#myForm').serializeArray());
    return str;
};
bootstrapTable();     
$("#sel_menu2").select2({
    tags: true,
});
$('.date').datetimepicker({
	language: 'zh-CN',
    minView: 4,
    autoclose: true,
    format : 'yyyy-mm-dd'
	}).on('changeDate',function(ev){
    let startTime = $('input[name="created_start"]').val()
    $('#end').datetimepicker('setStartDate',startTime)
});

function exports(){
    var cash_order = $('input[name="cash_order"]').val()
    var user_name = $('input[name="user_name"]').val()
    var user_code = $('input[name="user_code"]').val()
    var created_start = $('input[name="created_start"]').val()
    var created_end = $('input[name="created_end"]').val()
    var type = $('select[name="type[]"]').val()
    var status = $('select[name="status"]').val()
    var handle = $('select[name="handle"]').val()

    if(type == null)
    {
        type = ''
    }

    window.location.href = '<?php echo Url::toRoute(['cash/export']); ?>?cash_order='+cash_order+'&user_code='+user_code+
        '&status='+status+'&handle='+handle+'&user_name='+user_name+'&created_start='+created_start+'&type='+type+ '&created_end='+created_end
}

//提现审核通过
$('#handle-pass').click(function (){
    checkedbox= $("#table").bootstrapTable('getSelections');
    $ids = new Array();
    $("#startForm input[name='id[]']").remove();
    for(var i in checkedbox){
        $ids.push(checkedbox[i]['id']);
        $("#startForm").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
    }
    if($ids.length<=0){
        return false;
    }
    $('.handle-msg').text('您确定选中的提现记录审核通过吗？');
    $('input[name=handle]').val('<?= CashOrder::AUDIT_SUCCESS; ?>');
    $("#modal-handle").modal();
});

//提现审核失败
$('#handle-fail').click(function (){
    checkedbox= $("#table").bootstrapTable('getSelections');

    $ids = new Array();
    $("#startForm input[name='id[]']").remove();
    for(var i in checkedbox){
        $ids.push(checkedbox[i]['id']);
        $("#startForm").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
    }
    if($ids.length<=0){
        return false;
    }
    $('.handle-msg').text('您确定选中的提现记录审核不通过吗？');
    $('input[name=handle]').val('<?= CashOrder::AUDIT_FAIL; ?>');
    $("#modal-handle").modal();
});

function handle(e){

    $(e).attr('disabled', true);
    $(e).text('处理中...');

    $.ajax({
        type:"POST",
        url:"<?php echo Url::toRoute(['withdrawal']) ?>",
        data:$("#startForm").serialize(),
        dataType:'json',
        success:function(result){
            $(e).attr('disabled', false);
            $(e).text('确认');
        },
        complete : function(){
            $(e).attr('disabled', false);
            $(e).text('确认');
        }
    });
}

</script>