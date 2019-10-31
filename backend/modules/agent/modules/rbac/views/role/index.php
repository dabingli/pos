<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\entities\AgentUser;
$this->title = '用户管理';
$this->params['breadcrumbs'][] = '代理商信息';
$this->params['breadcrumbs'][] = '代理商平台管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/dist/bootstrap-table.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/dist/bootstrap-table.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                 
                  <div class="x_content">
                       <div class="panel-body">
                            <form onsubmit="return false" id="myForm" action="" method="get" class="form-horizontal">
                                <div class="form-group" style="margin-top:15px">
                                    <label class="control-label col-sm-1" for="account">数据权限名称</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="name" value="<?php echo \Yii::$app->request->get('name'); ?>" id="account">
                                    </div>
                                     <div class="col-sm-3" style="text-align:left;">
                                        <button onclick="bootstrapTable()" type="submit" class="btn btn-primary">查 询</button>
                                    	<a href="<?php echo Url::toRoute(['/agent/rbac/role/index']); ?>" class="btn btn-success">重 置</a>
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                    
                  </div>
                </div>
              </div>   
</div>

<div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                  <div class="x_content">
                   <div id="toolbar" class="btn-group">
                        <button  type="button" class="btn btn-default add">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                        </button>
                        <button disabled="disabled" id="btn_delete" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 删除
                        </button>
                        <!--  <button disabled="disabled" id="btn_item" type="button" class="btn btn-default">
                            <span class="" aria-hidden="true"></span> 资源分配 
                        </button>-->
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
				你确定需要删除选择的吗？
			</div>
			<div class="modal-footer">
				<form id="delForm" style="display:none">
               <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
				<button  type="button" class="btn btn-primary" onclick="delHaulBtn()">确认</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal -->
</div>  
<script>
$('#btn_delete').click(function (){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	$ids = new Array();
	$("#delForm input[name='id[]']").remove();
	for(var i in checkedbox){
		$ids.push(checkedbox[i]['id']);
		$("#delForm").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
	}
	if($ids.length<=0){
		return false;
	}
	$("#delOverhaul").modal();
});
function delHaulBtn(){
	$.ajax({
        type:"POST",
    		async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['/agent/rbac/role/delete']) ?>",
        data:$("#delForm").serialize(),
        dataType:'json',
        success:function(result){
        		location.reload();
        }
      });
}
function del($id){
	if($id==''){
		return false;
	}
	$("#delForm input[name='id[]']").remove();
	$("#delForm").append('<input type="hidden" name="id[]" value="'+$id+'" />');
	$("#delOverhaul").modal();
}

function stopHaulBtn(){
	$.ajax({
        type:"POST",
    		async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['/agent/rbac/user/stop']) ?>",
        data:$("#stopForm").serialize(),
        dataType:'json',
        success:function(result){
        		location.reload();
        }
      });
}

function edit($id){
	$(this).attr("disabled",true);  
	loading.show();
	$.ajax({
	    type:"POST",
		async:true,//false时为同步true为异步一般是异步
	    url:"<?php echo Url::toRoute(['/agent/rbac/role/add']) ?>",
	    data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
	    dataType:'json',
	    success:function(result){
	    	$('#myModal').html(result['html']);
			$('#myModal').modal();
	    },
	    beforeSend : function(){
            $(this).attr("disabled","true");   
        },
        complete : function(){
        		$(this).attr("disabled",false);   
        		loading.hide();
        }
	});
}
$(".add").click(function(){
	$.ajax({
	    type:"POST",
		async:true,//false时为同步true为异步一般是异步
	    url:"<?php echo Url::toRoute(['/agent/rbac/role/add']) ?>",
	    data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content')},
	    dataType:'json',
	    success:function(result){
	    	$('#myModal').html(result['html']);
			$('#myModal').modal();
	    }
	});
});
function getSelections(){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	if(checkedbox.length > 0){
		$('#btn_item').attr("disabled",false);
		$('#btn_delete').attr("disabled",false);   
	}else{
		$('#btn_item').attr("disabled",true);
		$('#btn_delete').attr("disabled",true); 
	}
}
$("#btn_item").click(function(){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	$arr= new Array();
	for(var i in checkedbox){
		$arr.push(checkedbox[i]['id']);
	}
	item($arr);
});
function item($arr){
	$.ajax({
	    type:"POST",
		async:true,//false时为同步true为异步一般是异步
	    url:"<?php echo Url::toRoute(['/agent/rbac/role/item']) ?>",
	    data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$arr},
	    dataType:'json',
	    success:function(result){
	    	$('#myModal').html(result['html']);
			$('#myModal').modal();
	    }
	});
}
function bootstrapTable(){
	$('#table').bootstrapTable('destroy');
	$('#table').bootstrapTable({
        url: '<?php echo Url::toRoute(['/agent/rbac/role/list']) ?>',         //请求后台的URL（*）
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
        }, 
        {
            field: 'name',
            title: '数据权限名称'
        }, 
        {
            field: 'agent_name',
            title: '机构名称'
        }, 
        {
            field: 'description',
            title: '备注'
        }, 
        {
            field: 'add_user',
            title: '创建人'
        }, 
        {
            field: 'created_at',
            title: '创建时间'
        }, 
        {
            field: 'update_user',
            title: '修改人'
        }, 
        {
            field: 'updated_at',
            title: '修改时间'
        },
        {
        	formatter: function (value, row, index) {
                return `<div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:edit(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 修改</font></font></a>
                  </li>
                  <li><a href="javascript:item(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 分配资源</font></font></a>
                  </li>
                  <li><a href="javascript:del(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="glyphicon glyphicon-remove"></i> 删除</font></font></a>
                  </li>
                </ul>`;
            },
            align: 'center',
            valign: 'middle',
            title: '操作'
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
	//console.log(params);
	//console.log($('#myForm').serializeArray());
    return params;
};
bootstrapTable();

</script>