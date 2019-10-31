<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\user\User;
use frontend\assets\AppAsset;
use common\models\agent\AgentRechargeLog;
$this->title = '充值管理';
$this->params['breadcrumbs'][] = '充值列表';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<link href="<?= \yii::$app->request->baseUrl . "/css/bootstrap-datetimepicker.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>

<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1">代付金余额</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" value="<?=$data['balance']?> 元" disabled  >
                        </div>

                        <label class="control-label col-sm-1">短信剩余条数</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" value="<?=$data['remaining_sms_number']?> 条" disabled >
                        </div>

                        <label class="control-label col-sm-1">实名剩余次数</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" value="<?=$data['remaining_real_name_auth_number']?> 次" disabled >
                        </div>
                    </div>

                    <form onsubmit="return false" id="settings" style="margin-top:15px">
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">代付金预警金额</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control required" name="warning_balance" value="<?=$data['warning_balance']?>">
                            </div>

                            <label class="control-label col-sm-1">短信预警条数</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control required" name="warning_sms_number" value="<?=$data['warning_sms_number']?>">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">实名预警次数</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control required" name="warning_real_name_auth_number" value="<?=$data['warning_real_name_auth_number']?>">
                            </div>

                            <label class="control-label col-sm-1">接收预警手机号</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control required" name="warning_mobile" value="<?=$data['warning_mobile']?>">
                            </div>
                            <button id="settings-submit" class="btn btn-info">保 存 预 警 设 置</button>
                        </div>
                    </form>

                    <form onsubmit="return false" id="recharge" class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1">充值类型</label>
                        <div class="col-sm-2">
                            <?php echo Html::dropDownList('recharge_type','',[''=>'全部']+AgentRechargeLog::typeLabels(),['class'=>'form-control']) ?>
                        </div>

                        <label class="control-label col-sm-1 recharge_sms recharge_text"  style="display: none">充值条数(条)</label>
                        <div class="col-sm-2 recharge_sms" style="display: none">
                            <input type="number" class="form-control" name="recharge_sms" value="">
                        </div>

                        <label class="control-label col-sm-1">充值金额(元)</label>
                        <div class="col-sm-2 ">
                            <input type="number" class="form-control recharge_money" name="recharge_money" value="">
                        </div>

                        <button  id="recharge-submit" class="btn btn-primary">充 值</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                <form onsubmit="return false" id="myForm"  class="form-horizontal">
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1">订单号</label>
                        <div class="col-sm-2">
                            <input placeholder="充值记录单号" type="text" class="form-control" name="recharge_no" value="">
                        </div>

                        <label class="control-label col-sm-1">充值类型</label>
                        <div class="col-sm-2">
                            <?php echo Html::dropDownList('type','',[''=>'全部']+AgentRechargeLog::typeLabels(),['class'=>'form-control']) ?>

                        </div>
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
                    </div>
                    <div class="form-group" style="margin-top:15px">
                        <label class="control-label col-sm-1">状态</label>
                        <div class="col-sm-2">
                            <?php echo Html::dropDownList('status','',[''=>'全部']+AgentRechargeLog::statusLabels(),['class'=>'form-control']) ?>
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
        <div class="box">
            <div class="panel-body">
                <div id="toolbar" class="btn-group">
                    <button onclick="exports()" type="button" class="btn btn-default">
                        导出
                    </button>
                    <button disabled="disabled" id="btn_del" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 删除
                    </button>
                </div>
                <table id="table" class="table table-striped table-bordered bulk_action">

                </table>
            </div>
        </div>
    </div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="rechargeModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <h4 class="modal-title" id="myModalLabel3">
                    提示
                </h4>
            </div>
            <div class="modal-body">
                你确定要充值吗？
            </div>
            <div class="modal-footer">
                <form id="startForm" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
                <button  type="button" class="btn btn-primary" onclick="recharge()">确认</button>
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
                你确定删除选择的订单吗？
            </div>
            <div class="modal-footer">
                <form id="startForm" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
                <button  type="button" class="btn btn-primary" onclick="delHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script>

    var isRequest = true;
    var smsUnitPrice = '<?= Yii::$app->debris->config('sms_unit_price') ?>';
    var realNameAuthUnitPrice = '<?= Yii::$app->debris->config('real_name_auth_unit_price') ?>';
    var csrfParam = '<?= \Yii::$app->request->csrfParam?>';

    function getSelections(){
        checkedbox= $("#table").bootstrapTable('getSelections');
        if(checkedbox.length > 0){
            $('#btn_del').attr("disabled",false);

        }else{
            $('#btn_del').attr("disabled",true);
        }
    }

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
                field: 'checkStatus',  //给多选框赋一个field值为“checkStatus”用于更改选择状态!!!!
                valign : 'middle',
                checkbox:true,
                formatter: function (value, row, index) {
                    if(row.o_status == '<?php echo AgentRechargeLog::WAIT_PAY; ?>'){
                        return {
                            disabled: false,
                        }
                    }else{
                        return {
                            disabled: true,
                        }
                    }

                }
            },{
                field: 'recharge_no',
                title: '订单号',
                align: 'center',
                width:200
            },
                {
                    field: 'type',
                    title: '充值类型',
                    align: 'center',
                    width:200
                },
                {
                    field: 'money',
                    title: '充值金额',
                    align: 'center',
                    width:200
                },
                {
                    field: 'created_at',
                    title: '充值时间',
                    align: 'center',
                    width:200
                },
                {
                    field: 'name',
                    title: '充值人',
                    align: 'center',
                    width:200
                },
                {
                    field: 'trade_no',
                    title: '交易号',
                    align: 'center',
                    width:200
                },
                {
                    field: 'status',
                    title: '状态',
                    align: 'center',
                    width: 200
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
    }

    bootstrapTable();

    $('.date').datetimepicker({
        language: 'zh-CN',
        minView: 4,
        autoclose: true,
        format : 'yyyy-mm-dd'
    });

    $('#btn_del').click(function (){
        checkedbox= $("#table").bootstrapTable('getSelections');
        // console.log(checkedbox);
        $ids = new Array();
        $("#startForm input[name='id[]']").remove();
        for(var i in checkedbox){
            $ids.push(checkedbox[i]['id']);
            $("#startForm").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
        }
        if($ids.length<=0){
            return false;
        }
        $("#startOverhaul").modal();
    });

    function delHaulBtn(){
        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['delete']) ?>",
            data:$("#startForm").serialize(),
            dataType:'json',
            success:function(result){
                location.reload();
            }
        });
    }

    //代理商信息导出
    function exports(){
        var recharge_no = $('input[name="recharge_no"]').val();
        var created_start = $('input[name="created_start"]').val();
        var created_end = $('input[name="created_end"]').val();
        var status = $('select[name="status"]').val();
        var type = $('select[name="type"]').val();
        window.location.href = '<?php echo Url::toRoute(['export']); ?>?recharge_no='+recharge_no+'&created_start='+created_start+'&created_end='+created_end+'&status='+status+'&type='+type;
    }

    function settings(){

        if(!isRequest){
            return ;
        }
        isRequest = false;
        $('#settings-submit').text('正在保存...');
        $('#settings-submit').attr('disabled', 'disabled');

        params = {};
        params[csrfParam]=$("[name='csrf-token']").attr('content');
        var f = $('#settings').serializeArray();
        for(var i in f){
            params[f[i]['name']] = f[i]['value'];
        }

        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['settings']) ?>",
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

    function showRecharge(){
        $('#rechargeModel').modal();
    }

    function recharge(){

        if(!isRequest){
            return ;
        }
        isRequest = false;
        $('#recharge-submit').text('正在充值...');
        $('#recharge-submit').attr('disabled', 'disabled');

        params = {};
        params[csrfParam]=$("[name='csrf-token']").attr('content');
        var f = $('#recharge').serializeArray();
        for(var i in f){
            params[f[i]['name']] = f[i]['value'];
        }

        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['recharge']) ?>",
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

    $('select[name=recharge_type]').on('change', function(){
        var type = $(this).val();

        switch (type) {
            case '<?= AgentRechargeLog::PAYMENT ?>':
                $('.recharge_sms').hide();
                $('.recharge_money').val('');
                $('.recharge_money').attr('readonly', false);
                break;
            case '<?= AgentRechargeLog::SMS ?>':
                $('.recharge_text').text('充值条数(条)');
                $('.recharge_sms').show();
                var sms = parseInt($('input[name=recharge_sms]').val());
                $('.recharge_money').val((sms * smsUnitPrice).toFixed(2));
                $('.recharge_money').attr('readonly', true);
                break;
            case '<?= AgentRechargeLog::REAL_NAME ?>':
                $('.recharge_text').text('充值次数(次)');
                $('.recharge_sms').show();
                var auth = parseInt($('input[name=recharge_sms]').val());
                $('.recharge_money').val((auth * realNameAuthUnitPrice).toFixed(2));
                $('.recharge_money').attr('readonly', true);
                break;
            default:
                $('.recharge_sms').hide();
                $('.recharge_money').val('');
                $('.recharge_money').attr('readonly', false);
                break;
        }
    });

    $('input[name=recharge_sms]').keyup(function(){

        var price = $('select[name=recharge_type]').val() == '<?= AgentRechargeLog::SMS ?>' ? smsUnitPrice : realNameAuthUnitPrice;

        var num = parseInt($(this).val());
        $('.recharge_money').val((num * price).toFixed(2));
    });

    function validate(){
        var min = 0, max = 90000000;
        $("#settings").validate({
            ignore: [],
            rules: {
                'warning_balance' : {
                    required : true,
                    min :　min,
                    max : max
                },
                'warning_sms_number' : {
                    required : true,
                    min :　min,
                    max : max
                },
                'warning_mobile' : {
                    required : true,
                    rangelength : [11,11]
                }

            },
            messages: {
                'warning_balance': {
                    required : '请输入代付金预警金额',
                    min : '代付金预警金额不得小于' + min,
                    max : '代付金预警金额不超过' + max
                },
                'warning_sms_number': {
                    required : '请输入预警短信条数',
                    min : '预警短信条数不得小于' + min,
                    max : '预警短信条数不超过' + max
                },
                'warning_real_name_auth_number': {
                    required : '请输入实名认证预警次数',
                    min : '实名认证预警次数不得小于' + min,
                    max : '实名认证预警次数不超过' + max
                },
                'warning_mobile': {
                    required : '请输入预警手机号',
                    rangelength : '请输入正确的预警手机号'
                },
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function (form) {
                settings();
            }
        });

        $.validator.addMethod("checkSMS",
            function(value, element) {

                var type = $('select[name=recharge_type]').val();
                if('<?= AgentRechargeLog::SMS ?>' == type){

                    var re = /^[0-9]+$/ ;
                    if(!(re.test(value))){
                        return false;
                    }

                    if(value <= 0){
                        return false;
                    }

                    if(value > 999999999){
                        return false;
                    }
                }

                return true;
            },
            "充值短信条数应该是一个正整数(1~999999999)");

        $.validator.addMethod("checkRechargeMoney",
            function(value, element) {

                var type = $('select[name=recharge_type]').val();
                if('<?= AgentRechargeLog::PAYMENT ?>' == type){

                    if(value < 5000){
                        return false;
                    }
                }

                return true;
            },
            "充值金额必须大于5000");

        $("#recharge").validate({
            ignore: [],
            rules: {
                'recharge_type' : {
                    required : true,
                },
                'recharge_money' : {
                    checkRechargeMoney : true,
                },
                'recharge_sms' : {
                    checkSMS : true,
                }
            },
            messages: {
                'recharge_type': {
                    required : '请选择充值类型'
                },
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function (form) {
                showRecharge();
            }
        });
    }

    validate();

</script>