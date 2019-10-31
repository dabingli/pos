<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = '任务管理';
$this->params['breadcrumbs'][] = '自动化任务管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
                <div class="panel-body">
                       <div class="panel-body">
                            <form onsubmit="return false" id="myForm" action="<?php echo Url::toRoute('index'); ?>" method="get" class="form-horizontal">
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1" for="account">任务名称</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="name" value="">
                                    </div>
									<label class="control-label col-sm-1" for="account">任务操作</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="route" value="">
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
        <!-- 具体内容 -->
        <div class="box">
            <div class="panel-body">
                <div id="toolbar" class="btn-group">
                    <button  type="button" id="btn_add" class="btn btn-default add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                    </button>
                    <button disabled="disabled" id="btn_del" type="button" class="btn btn-danger">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> 删除
                    </button>
                </div>
                <table id="table" class="table">

                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 100px;">

</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="delOverhaul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定需要删除吗？
            </div>
            <div class="modal-footer">
                <form id="delFrom" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
                <button  type="button" class="btn btn-primary" onclick="DelHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<script>
$('#btn_del').click(function (){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	$ids = new Array();
	$("#delFrom input[name='id[]']").remove();
	for(var i in checkedbox){
		$ids.push(checkedbox[i]['id']);
		$("#delFrom").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
	}
	if($ids.length<=0){
		return false;
	}
	$("#delOverhaul").modal();
});
function DelHaulBtn(){
	$.ajax({
        type:"POST",
    		async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['del']) ?>",
        data:$("#delFrom").serialize(),
        dataType:'json',
        success:function(result){
        	location.reload();
        }
      });
}
function del($id){
	$("#delFrom input[name='id[]']").remove();
	$("#delFrom").append('<input type="hidden" name="id[]" value="'+$id+'" />');
	$("#delOverhaul").modal();
}
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
function edit($id){
	$.ajax({
	    type:"POST",
		async:true,//false时为同步true为异步一般是异步
	    url:"<?php echo Url::toRoute(['edit']) ?>",
	    data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
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
}
function getSelections(){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	if(checkedbox.length > 0){
		$('#btn_del').attr("disabled",false);  
	}else{
		$('#btn_del').attr("disabled",true);
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
        pageList: [10, 25],        //可供选择的每页的行数（*）
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
            field: 'id',
            title: '任务ID'
        },
        {
            field: 'name',
            title: '任务名称'
        },
        {
            field: 'route',
            title: '任务操作'
        },
        {
            field: 'num',
            title: '任务条数'
        },
        {
            field: 'numing',
            title: '当前任务进行数量'
        },
        {
            field: 'crontab',
            title: '运行格式'
        },
        {
            field: 'remarks',
            title: '备注'
        },
        {
        	formatter: function (value, row, index) {
                return `<button onclick="edit('`+row['id']+`')"  type="button" class="btn btn-primary">
                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> 编辑
                </button>
                <button onclick="del('`+row['id']+`')"  type="button" class="btn btn-danger">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> 删除
                </button>`;
            },
            align: 'center',
            valign: 'middle',
            title: '操作',
            width: 180
        }]
    });
}
//得到查询的参数
function queryParams (params) {
	params['<?= \Yii::$app->request->csrfParam?>']=$("[name='csrf-token']").attr('content');
	var f = $('#myForm').serializeArray();
	for(var i in f){
		params[f[i]['name']] = f[i]['value'];
	}
    return params;
};
bootstrapTable();
</script>