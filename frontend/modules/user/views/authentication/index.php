<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\user\UserIdentityAudit;
use backend\assets\AppAsset;
$this->title = '代理商信息';
$this->params['breadcrumbs'][] = '代理商信息';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/jquery-treegrid/css/jquery.treegrid.css"?>" rel="stylesheet" />
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>

<script type="text/javascript" src="<?=\yii::$app->request->baseUrl?>/dist/extensions/treegrid/bootstrap-table-treegrid.min.js"></script>
<script type="text/javascript" src="<?=\yii::$app->request->baseUrl?>/jquery-treegrid/js/jquery.treegrid.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                <form onsubmit="return false" id="myForm" action="<?php echo Url::toRoute('/user/authentication/index'); ?>" method="get" class="form-horizontal">
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1">代理商编号</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="user_code" value="">
                        </div>
                        <label class="control-label col-sm-1">审核人</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="audit_name" value="">
                        </div>
                        <label class="control-label col-sm-1">手机号</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="mobile" value="">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1">状态</label>
                        <div class="col-sm-3">
                            <?php echo Html::dropDownList('status','',[''=>'全部']+UserIdentityAudit::getStatusLabels(),['class'=>'form-control']) ?>
                        </div>
                        <label class="control-label col-sm-1">审核时间</label>
                        <div class="col-sm-2">
                            <div class='input-group date'>
                                <input placeholder="请输入审核时间" value="" name="audit_at_start"  type='date' class="form-control" />
                                <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class='input-group date' id="end">
                                <input placeholder="请输入审核时间" value="" name="audit_at_end"  type='date' class="form-control" />
                                <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                            </div>
                        </div>
                        
                        <div class="col-sm-3" style="text-align:left;">
                            <button onclick="bootstrapTable()" type="submit" class="btn btn-primary">查 询</button>
                            <a href="<?php echo Url::toRoute(['/user/authentication/index']); ?>" class="btn btn-success">重 置</a>
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
<!--                <div id="toolbar" class="btn-group">-->
<!--                    <button onclick="exports()" type="button" class="btn btn-default">-->
<!--                        导出-->
<!--                    </button>-->
<!--                    <button disabled="disabled" id="btn_start" type="button" class="btn btn-default">-->
<!--                        <span class="glyphicon glyphicon-star" aria-hidden="true"></span> 启用-->
<!--                    </button>-->
<!--                    <button disabled="disabled" id="btn_stop" type="button" class="btn btn-default">-->
<!--                        <span class="glyphicon glyphicon-stop" aria-hidden="true"></span> 停用-->
<!--                    </button>-->
<!--                    <button disabled="disabled" id="btn_frozen" type="button" class="btn btn-default">-->
<!--                        <span class="glyphicon glyphicon-registration-mark" aria-hidden="true"></span> 冻结收益-->
<!--                    </button>-->
<!--                </div>-->
                <table id="table" class="table table-striped table-bordered bulk_action">

                </table>
            </div>
        </div>
    </div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

</div>

<script>


    function getSelections(){
        checkedbox= $("#table").bootstrapTable('getSelections');
        console.log(checkedbox);
        if(checkedbox.length > 0){
            $('#btn_start').attr("disabled",false);
            $('#btn_stop').attr("disabled",false);
            $('#btn_frozen').attr("disabled",false);

        }else{
            $('#btn_start').attr("disabled",true);
            $('#btn_stop').attr("disabled",true);
            $('#btn_frozen').attr("disabled",true);
        }
    }
    function bootstrapTable(){
        var $table = $("#table");
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
            //height: 500,                        //行高，如果没有设置height属性，表格自动根据记录条数觉得表格高度
            onLoadSuccess: function(data) {

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
                field: 'id',
                title: '实名认证编号',
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
                    field: 'real_name',
                    title: '姓名',
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
                    field: 'identity_card',
                    title: '证件号',
                    align: 'center',
                    width:200
                },
                {
                    field: 'type',
                    title: '证件类型',
                    align: 'center',
                    width:200
                },
                {
                    field: 'cardNo',
                    title: '银行卡号',
                    align: 'center',
                    width:200
                },
                {
                    field: 'status',
                    title: '认证状态',
                    align: 'center',
                    width:200
                },
                {
                    field: 'audit_name',
                    title: '审核人',
                    align: 'center',
                    width:200
                },
                {
                    field: 'audit_at',
                    title: '审核时间',
                    align: 'center',
                    width:200
                },
                {
                    field: 'description',
                    title: '审核说明',
                    align: 'center',
                    width:200
                },
                {
                    field: 'created_at',
                    title: '申请时间',
                    align: 'center',
                    width:200
                },
                {
                    formatter: function (value, row, index) {
                        $str=`<div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                    <ul class="dropdown-menu">
                      <li><a href="javascript:view(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 查看详情</font></font></a>
                      </li>`;
                        $str+=`</ul>`;
                        return $str;

                    },
                    align: 'center',
                    valign: 'middle',
                    title: '操作'
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
    };
    bootstrapTable();
    $('.date').datetimepicker({
        language: 'zh-CN',
        minView: 4,
        autoclose: true,
        format : 'yyyy-mm-dd'
    }).on('changeDate',function(ev){
    let startTime = $('input[name="audit_at_start"]').val()
    $('#end').datetimepicker('setStartDate',startTime)
});

    function view($id){
        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['view']) ?>",
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$id},
            dataType:'json',
            success:function(result){
                $('#myModal').html(result['html']);
                $('#myModal').modal();
            },
            complete : function(){
                // loading.hide();

            }
        });
    }

</script>