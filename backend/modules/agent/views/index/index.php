<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\RegionWidget;
use backend\assets\AppAsset;
use common\models\agent\Agent;
$this->title = '产品类型';
$this->params['breadcrumbs'][] = '产品管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<div class="row">
    <div class="col-sm-12">
        <!-- 具体内容 -->
        <div class="box">
				<div class="panel-body">
                    <form id="myForm" onsubmit="return bootstrapTable($(this))" action="" method="get" class="form-horizontal">
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1" for="name">服务商名称</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="name" value="" id="name">
                            </div>
                            <label class="control-label col-sm-1" for="name">联系人</label>
                            <div class="col-sm-3">
                            	
                                <input type="text" class="form-control" name="contacts" value="" >
                            </div>
                            <label class="control-label col-sm-1" for="add_user_name">省份</label>
                            <div class="col-sm-3">
                                <?php echo RegionWidget::widget(['name'=>'province_id','options'=>['class'=>'form-control','onchange'=>'onchangecity($(this),"city")']]); ?>
                            </div>  
                             
                        </div>
                        <div class="form-group" style="margin-top:15px">
                        	<label class="control-label col-sm-1" for="add_user_name">城市</label>
                            <div class="col-sm-3">
                                <?php echo RegionWidget::widget(['name'=>'city_id','region_id'=>false,'options'=>['class'=>'form-control','id'=>'city']]); ?>
                            </div> 
                            <label class="control-label col-sm-1" for="add_user_name">状态</label>
                            <div class="col-sm-3">
                            <?php echo html::dropDownList('status','',[''=>'全部']+Agent::statusLabels(),['class'=>'form-control']) ?>
                            </div> 
                            <div class="col-sm-4" style="text-align:left;">
                                <button type="submit" class="btn btn-primary">查 询</button>
                            	<a href="<?php echo Url::toRoute(['index']); ?>" class="btn btn-success">重 置</a>
                            </div>
                        </div>
                        
                        <!-- <div class="form-group" style="margin-top:15px">
                        	<div class="col-sm-3">
                        	</div>
                        	<
                        </div> -->
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
                        <button id="btn_add" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                        </button>
                        <button disabled id="btn_start" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span> 启用
                        </button>
                        <button disabled id="btn_delete" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-stop" aria-hidden="true"></span> 停用
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
<div class="modal fade" id="delcfmOverhaul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定需要停用吗？
            </div>
            <div class="modal-footer">
                <form id="modalFrom" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>

                <button  type="button" class="btn btn-primary" onclick="deleteHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="startOverhaul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定需要启用吗？
            </div>
            <div class="modal-footer">
                <form id="startFrom" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
                <button  type="button" class="btn btn-primary" onclick="startHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
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
        pageSize: <?php echo Yii::$app->debris->config('sys_page'); ?>,                       //每页的记录行数（*）
        pageList: [10, 20, 30],        //可供选择的每页的行数（*）
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
        }, {
            field: 'name',
            title: '服务商名称'
        }, {
            field: 'admin_name',
            title: '服务商后台名称'
        }, {
            field: 'number',
            title: '服务商编号'
        }, {
            field: 'contract_date',
            title: '签约日期'
        }, {
            field: 'province',
            title: '省份'
        },
        {
            field: 'city',
            title: '地市'
        },
        {
            field: 'contacts',
            title: '联系人'
        },
        {
            field: 'mobile',
            title: '联系电话'
        },
        {
            field: 'mailbox',
            title: '联系邮箱'
        },
        {
            field: 'agent_fee',
            title: '平台手续费'
        },
        {
            field: 'expired_days',
            title: '有效期天数'
        },
        {
            field: 'status',
            title: '状态'
        },
        {
        	formatter: function (value, row, index) {
        		return `<div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                <ul class="dropdown-menu">
                <li><a href="javascript:view(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="glyphicon glyphicon-eye-open"></i> 详情</font></font></a>
                  </li>
                  <li><a href="javascript:edit(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 修改</font></font></a>
                  </li>
                  <li><a href="javascript:feeEdit(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 手续费设置</font></font></a>
                  </li>
                  <li><a href="javascript:rechargeDays(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 充值天数</font></font></a>
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


$('#btn_start').click(function (){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	$ids = new Array();
	$("#startFrom input[name='id[]']").remove();
	for(var i in checkedbox){
		$ids.push(checkedbox[i]['id']);
		$("#startFrom").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
	}
	if($ids.length<=0){
		return false;
	}
	$("#startOverhaul").modal();
});
function startHaulBtn(){
	$.ajax({
        type:"POST",
    		async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['start']) ?>",
        data:$("#startFrom").serialize(),
        dataType:'json',
        success:function(result){
        	location.reload();
        }
      });
}
$('#btn_delete').click(function (){
	
	//var checkedbox= $("#table").bootstrapTable('getSelections');
	//console.log(checkedbox);
	//alert(checkedbox);
	
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	$ids = new Array();
	$("#modalFrom input[name='id[]']").remove();
	for(var i in checkedbox){
		$ids.push(checkedbox[i]['id']);
		$("#modalFrom").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
	}
	if($ids.length<=0){
		return false;
	}
	$("#delcfmOverhaul").modal({
        backdrop : 'static',
        keyboard : false
    });
});
function deleteHaulBtn(){
	$.ajax({
        type:"POST",
    		async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['stop']) ?>",
        data:$("#modalFrom").serialize(),
        dataType:'json',
        success:function(result){
        	location.reload();
        }
      });
}
$("#btn_add").click(function(){
	$.ajax({
	    type:"POST",
		async:true,//false时为同步true为异步一般是异步
	    url:"<?php echo Url::toRoute(['/agent/index/add']) ?>",
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
	if($id==''){
		return false;
	}
	$.ajax({
	    type:"POST",
		async:true,//false时为同步true为异步一般是异步
	    url:"<?php echo Url::toRoute(['/agent/index/edit']) ?>",
	    data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
	    dataType:'json',
	    success:function(result){
	    	$('#add').html(result['html']);
			$('#add').modal();
	    }
	});
}

function feeEdit($id){
    if($id==''){
        return false;
    }
    $.ajax({
        type:"POST",
        async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['/agent/index/fee-edit']) ?>",
        data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
        dataType:'json',
        success:function(result){
            $('#add').html(result['html']);
            $('#add').modal();
        }
    });
}

function rechargeDays($id){
    if($id==''){
        return false;
    }
    $.ajax({
        type:"POST",
        async:true,//false时为同步true为异步一般是异步
        url:"<?php echo Url::toRoute(['/agent/index/recharge-days']) ?>",
        data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
        dataType:'json',
        success:function(result){
            $('#add').html(result['html']);
            $('#add').modal();
        }
    });
}
/*$('.date').datetimepicker({
	language: 'zh-CN',
    minView: 4,
    autoclose: true,
    format : 'yyyy-mm-dd'
});*/
function menu($id){
	// loading.show();
	$.ajax({
	    type:"POST",
	    url:"<?php echo Url::toRoute(['/agent/index/menu']) ?>",
	    data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
	    dataType:'json',
	    success:function(result){
	    	$('#add').html(result['html']);
			$('#add').modal();
	    },
	    complete : function(){
    			//l.stop();
    			//$(this).attr("disabled",false);   
    			// loading.hide();
    		}
	});
}

//查看详情
function view($id){
    $.ajax({
        type:"POST",
        url:"<?php echo Url::toRoute(['/agent/index/view']) ?>",
        data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
        dataType:'json',
        success:function(result){
            $('#add').html(result['html']);
            $('#add').modal();
        },
        complete : function(){
        }
    });
}

function getSelections(){
	checkedbox= $("#table").bootstrapTable('getSelections');
	console.log(checkedbox);
	if(checkedbox.length > 0){
		$('#btn_start').attr("disabled",false);
		$('#btn_stop').attr("disabled",false); 
		$('#btn_delete').attr("disabled",false);   
	}else{
		$('#btn_start').attr("disabled",true);
		$('#btn_stop').attr("disabled",true); 
		$('#btn_delete').attr("disabled",true); 
	}
}
function onchangecity(e,id){
	 $.ajax({
          type:"GET",
          url:"<?php echo Url::toRoute(['/site/region']) ?>?region_id="+e.val(),
          dataType:'json',
          success:function(result){
	            var s = '<option value="" selected="">请选择</option>'
				for(var r in result['data']){
					s+=`<option value="`+result['data'][r]['id']+`">`+result['data'][r]['title']+`</option>`
				}
				if(id!=''){
					$('#'+id).html(s);
				}
          }
    });
}
</script>