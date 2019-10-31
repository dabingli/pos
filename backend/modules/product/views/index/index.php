<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\RegionWidget;
use backend\assets\AppAsset;
use common\models\entities\Agent;
$this->title = '机具类型';
$this->params['breadcrumbs'][] = '基础信息';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/dist/bootstrap-table.min.css"?>" rel="stylesheet" />
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/dist/bootstrap-table.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<div class="row">
	<div class="col-sm-12">
        <div class="box">
        		<div class="panel-body">
                    <form id="myForm" onsubmit="return bootstrapTable($(this))" action="" method="get" class="form-horizontal">
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1" for="name">机具类型</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="name" value="" id="name">
                            </div>
                            <div class="col-sm-4" style="text-align:left;">
                                <button type="submit" class="btn btn-primary">查 询</button>
                            	<a href="<?php echo Url::toRoute(['/product/index/index']); ?>" class="btn btn-success">重 置</a>
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
                        <button id="btn_add" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                        </button>
               	 	</div>
                    <table id="table" class="table table-striped table-bordered bulk_action">
                      
                    </table>
                  </div>
                </div>
              </div> 
              </div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    
</div>
<script type="text/javascript">
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
        }, {
            field: 'name',
            title: '机具类型'
        }, {
            field: 'activation_money',
            title: '激活金额'
        },{
            field: 'add_user',
            title: '创建人'
        }, {
            field: 'created_at',
            title: '创建时间'
        },
        {
        	formatter: function (value, row, index) {
        		return `<div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                <ul class="dropdown-menu">
               
                  <li><a href="javascript:edit(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 修改</font></font></a>
                  </li>
                </ul>`;
            },
            align: 'center',
            valign: 'middle',
            title: '操作'
        }]
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
	//console.log(params);
	//console.log($('#myForm').serializeArray());
    return params;
};
bootstrapTable();



$("#btn_add").click(function(){
    // loading.show();
    // var l = Ladda.create(this);
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
            // l.start();
            $(this).attr("disabled","true");   
        },
        complete : function(){
        		// l.stop();
        		$(this).attr("disabled",false);   
        		// loading.hide();
        }
	});
});
function edit($id){
	if($id==''){
		return false;
	}
	$.ajax({
	    type:"POST",
		async:true,//false时为同步true为异步一般是异步
	    url:"<?php echo Url::toRoute(['add']) ?>",
	    data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
	    dataType:'json',
	    success:function(result){
	    	$('#add').html(result['html']);
			$('#add').modal();
	    }
	});
}

function getSelections(){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	if(checkedbox.length > 0){
		//$('#btn_start').attr("disabled",false);
		//$('#btn_stop').attr("disabled",false); 
		//$('#btn_delete').attr("disabled",false);   
	}else{
		//$('#btn_start').attr("disabled",true);
		///$('#btn_stop').attr("disabled",true); 
		//$('#btn_delete').attr("disabled",true); 
	}
}

</script>