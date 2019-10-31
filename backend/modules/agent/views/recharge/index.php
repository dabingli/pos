<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\user\User;
use frontend\assets\AppAsset;
use common\models\agent\AgentRechargeLog;
$this->title = '代理商充值管理';
$this->params['breadcrumbs'][] = '代理商充值管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                    <form onsubmit="return false" id="myForm" class="form-horizontal">
                        <div class="form-group" style="margin-top:15px">

                            <label class="control-label col-sm-1">服务商名称</label>
                            <div class="col-sm-2">
                                <input placeholder="服务商名称" type="text" class="form-control" name="real_name" value="">
                            </div>

                            <label class="control-label col-sm-1">服务商手机</label>
                            <div class="col-sm-2">
                                <input placeholder="服务商手机号" type="text" class="form-control" name="agent_phone" value="">
                            </div>

                            <label class="control-label col-sm-1">服务商编号</label>
                            <div class="col-sm-2">
                                <input placeholder="服务商编号" type="text" class="form-control" name="agent_number" value="">
                            </div>

                        </div>
                        <div class="form-group" style="margin-top:15px">

                            <label class="control-label col-sm-1">订单号</label>
                            <div class="col-sm-2">
                                <input placeholder="充值记录单号" type="text" class="form-control" name="recharge_no" value="">
                            </div>

                            <label class="control-label col-sm-1">状态</label>
                            <div class="col-sm-2">
                                <?php echo Html::dropDownList('status','',[''=>'全部']+AgentRechargeLog::statusLabels(),['class'=>'form-control']) ?>
                            </div>

                            <label class="control-label col-sm-1">充值类型</label>
                            <div class="col-sm-2">
                                <?php echo Html::dropDownList('type','',[''=>'全部']+AgentRechargeLog::typeLabels(),['class'=>'form-control']) ?>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:15px">

                            <label class="control-label col-sm-1">充值日期</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请选择充值日期" value="" name="created_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请选择充值日期" value="" name="created_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>

                            <div class="col-sm-3" style="text-align:center;">
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
                    <button onclick="exports()" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span>
                        导出
                    </button>
                </div>
                <table id="table" class="table table-striped table-bordered bulk_action">

                </table>
            </div>
        </div>
    </div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>

<!-- 模态框（Modal） 查看详情 -->
<div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 100px;"></div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="pay-success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定该笔充值订单已支付吗？
            </div>
            <div class="modal-footer">
                <button  type="button" class="btn btn-primary" onclick="paySuccessAjax()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="pay-close" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定要结束该笔充值订单吗？
            </div>
            <div class="modal-footer">
                <button  type="button" class="btn btn-primary" onclick="payCloseAjax()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script>

    var isRequest = true;
    var rechargeNo = '';
    var csrfParam = '<?= \Yii::$app->request->csrfParam?>';

    function bootstrapTable(){
        var $table = $("#table");
        $('#table').bootstrapTable('destroy');
        $('#table').bootstrapTable({
            url: '<?php echo Url::toRoute(['list']) ?>',         //请求后台的URL（*）
            method: 'get',                      //请求方式（*）
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
                // getSelections();
            },
            onCheckAll:function(rows){
                // getSelections();
            },
            onUncheck:function(row){
                //console.log(row);
                // getSelections();
            },
            onUnCheckAll:function(rows){
                // getSelections();
                //alert(111);
            },
            columns: [{
                field: 'recharge_no',
                title: '订单号',
                align: 'center',
            },
                {
                    field: 'type',
                    title: '充值类型',
                    align: 'center',
                },
                {
                    field: 'money',
                    title: '充值金额',
                    align: 'center',
                },
                {
                    field: 'created_at',
                    title: '充值时间',
                    align: 'center',
                },
                {
                    field: 'real_name',
                    title: '服务商',
                    align: 'center',
                },
                {
                    field: 'mobile',
                    title: '手机号',
                    align: 'center',
                },
                {
                    field: 'trade_no',
                    title: '交易号',
                    align: 'center',
                },
                {
                    field: 'pay_at',
                    title: '审核时间',
                    align: 'center',
                },
                {
                    field: 'audit_name',
                    title: '审核人',
                    align: 'center',
                },
                {
                    field: 'status',
                    title: '状态',
                    align: 'center',
                },
                {
                    formatter: function (value, row, index) {
                        var html = `<div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                                    <ul class="dropdown-menu">`;
                        html += `<li><a href="javascript:view('`+row.id+`')"><font style="vertical-align: inherit;"><i class="glyphicon glyphicon-eye-open"></i> 详情 </font></font></a></li>`;

                        if(row.o_status == 1){
                            html += `<li><a href="javascript:paySuccess('`+row.recharge_no+`')"><font style="vertical-align: inherit;"><i class="fa fa-edit"></i> 交易成功 </font></font></a></li>
                            <li><a href="javascript:payClose('`+row.recharge_no+`')"><font style="vertical-align: inherit;"><i class="fa fa-minus-square"></i> 关闭订单 </font></font></a></li>`;

                        }

                        html += `</ul>`;


                        return html;
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
    });

    //代理商信息导出
    function exports(){
        var real_name = $('input[name="real_name"]').val();
        var agent_phone = $('input[name="agent_phone"]').val();
        var agent_number = $('input[name="agent_number"]').val();
        var recharge_no = $('input[name="recharge_no"]').val();
        var created_start = $('input[name="created_start"]').val();
        var created_end = $('input[name="created_end"]').val();
        var status = $('select[name="status"]').val();
        var type = $('select[name="type"]').val();
        window.location.href = '<?php echo Url::toRoute(['export']); ?>?real_name='+real_name+'&agent_phone='+agent_phone+'&agent_number='+agent_number+'&recharge_no='+recharge_no+'&created_start='+created_start+'&created_end='+created_end+'&status='+status+'&type='+type;
    }

    function view(id){
        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['view']) ?>",
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'), 'id':id},
            dataType:'json',
            success:function(result){
                $('#view').html(result['html']);
                $('#view').modal();
            },
            beforeSend : function(){
                $(this).attr("disabled","true");
            },
            complete : function(){
                $(this).attr("disabled",false);
            }
        });
    }

    function paySuccess(no){
        if(!no){
            return false;
        }
        rechargeNo = no;
        $("#pay-success").modal();
    }

    function payClose(no){
        if(!no){
            return false;
        }
        rechargeNo = no;
        $("#pay-close").modal();
    }

    function paySuccessAjax(){

        if(!rechargeNo){
            return false;
        }

        params = {};
        params['<?= \Yii::$app->request->csrfParam?>']=$("[name='csrf-token']").attr('content');
        params['recharge_no'] = rechargeNo;

        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['pay-success']) ?>",
            data:params,
            dataType:'json',
            success:function(result){
                isRequest = true;
            },
            complete : function(){
                isRequest = true;
            }
        });
    }

    function payCloseAjax(){

        if(!rechargeNo){
            return false;
        }

        params = {};
        params['<?= \Yii::$app->request->csrfParam?>']=$("[name='csrf-token']").attr('content');
        params['recharge_no'] = rechargeNo;

        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['pay-close']) ?>",
            data:params,
            dataType:'json',
            success:function(result){
                isRequest = true;
            },
            complete : function(){
                isRequest = true;
            }
        });
    }

</script>