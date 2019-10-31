<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
$this->title = '添加权限';
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
                  <div class="x_title">
                    <h2><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">添加权限</font></font><small><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"></font></font></small></h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
					<?php if(Yii::$app->request->get('id')){ ?>
					<form method="post" action="<?php echo Url::toRoute(['/agent/rbac/perm/update']); ?>" class="form-horizontal form-label-left">
					<input type="hidden" name="id" value="<?php echo Yii::$app->request->get('id') ?>">
					<?php }else{ ?>
                    <form method="post" action="<?php echo Url::toRoute(['/agent/rbac/perm/create']); ?>" class="form-horizontal form-label-left">
					<?php } ?>
					<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" >权限名称<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input value="<?php echo $perm->name; ?>" type="text"  name="name" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>


                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="textarea">路由 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea style="height: 200px" name="routes" class="form-control col-md-7 col-xs-12"><?php echo $routes; ?></textarea>
                        </div>
                      </div>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                          <a href="<?php echo Url::toRoute(['/agent/rbac/role/add-rule']) ?>"; class="btn btn-primary">重置</a>
                          <button type="submit" class="btn btn-success">提交</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                 
                  <div class="x_content">
                       <div class="panel-body">
                            <form onsubmit="return false" id="myForm" method="get" class="form-horizontal">
                                <div class="form-group" style="margin-top:15px">
                                    
                                    <div class="col-sm-4">
                                        <input placeholder="输入权限名或路由" type="text" class="form-control" name="keyword" value="<?php echo \Yii::$app->request->get('keyword'); ?>" id="keyword">
                                    </div>
                                     <div class="col-sm-3" style="text-align:left;">
                                        <button onclick="bootstrapTable()" type="submit" class="btn btn-primary">查 询</button>
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
                   
					<table id="table" class="table table-striped table-bordered bulk_action">
                      
                    </table>
                  </div>
                </div>
              </div> 
              </div>
<script>
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
    			alert(111);
    		},
        columns: [{
            checkbox: true
        },
        {
            field: 'id',
            title: 'ID'
        },
        {
            field: 'name',
            title: '权限名称'
        }, 
        {
            field: 'route',
            title: '路由'
        },
        {
        	formatter: function (value, row, index) {
                return `<div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:edit(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 修改</font></font></a>
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
function edit($id){
	location.href="<?php echo Url::toRoute(['/agent/rbac/perm/index']) ?>?id="+$id;
}

</script>