<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\entities\ProductType;
$this->title = 'APP消息管理';
$this->params['breadcrumbs'][] = 'APP管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/fileinput/js/fileinput.js"></script>
<link rel="stylesheet" href="<?=\yii::$app->request->baseUrl?>/fileinput/css/fileinput.css">
<script src="<?=\yii::$app->request->baseUrl?>/fileinput/js/locales/zh.js"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                <div id="toolbar" class="btn-group">
                    <button id="btn_add" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-plus"></span>
                       	新  增
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
<script type="text/javascript">
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
function bootstrapTable(){
    $('#table').bootstrapTable('destroy');
    $('#table').bootstrapTable({
        url: '<?php echo Url::toRoute(['list']) ?>',         //请求后台的URL（*）
        method: 'post',                      //请求方式（*）
        toolbar: '#toolbar',                //工具按钮用哪个容器
        striped: true,                      //是否显示行间隔色
        cache: false,                       //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
        pagination: true,                   //是否显示分页（*）
        sortable: false,                     //是否启用排序
        sortName: 'bank', // 要排序的字段
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
        uniqueId: "bank",                     //每一行的唯一标识，一般为主键列
        showToggle:false,                    //是否显示详细视图和列表视图的切换按钮
        cardView: false,                    //是否显示详细视图
        detailView: false,                   //是否显示父子表
        selectItemName:'bank[]',
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
            field: 'title',
            title: '内容名称'
        },
        {
            field: 'content',
            title: '内容名称'
        },
        {
            field: 'content',
            title: '内容名称'
        },
        {
            formatter: function (value, row, index) {
                str = '';
                for(var i in  row['images']){
                	str+=`<img style="float: none;" class="img-circle rf-img-md images" src="`+row['images'][i]+`" /> `;
                }
                return str;
            },
            valign: 'middle',
            title: '图片'
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
        }],
    });
    return false;
}

function getSelections(){
    checkedbox= $("#table").bootstrapTable('getSelections');
    if(checkedbox.length > 0){
        //$('#btn_start').attr("disabled",false);
        //$('#btn_close').attr("disabled",false);
    }else{
        //$('#btn_start').attr("disabled",true);
        //$('#btn_close').attr("disabled",true);
    }
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
$('#btn_add').click(function(){
    var that = $(this);
    that.attr("disabled",true);
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
            that.attr("disabled",true);
        },
        complete : function(){
        	that.attr("disabled",false);
        }
    });
})
$('body').on('click','.images' ,function () {
	$('#imgsrc').modal();
    $('#imgsrc img').attr('src',$(this).attr('src'));
});
</script>