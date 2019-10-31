<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\user\User;
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

<style>
    .replace_nbsp{
        text-align: left;

    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="panel-body">
                    <form onsubmit="return false" id="myForm" action="<?php echo Url::toRoute('index'); ?>" method="get" class="form-horizontal">
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">代理商编号</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="user_code" value="">
                            </div>
                            <label class="control-label col-sm-1">机构名称</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="user_name" value="">
                            </div>
                            <label class="control-label col-sm-1">手机号</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="mobile" value="">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">身份证号码</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="identity" value="">
                            </div>
                            <label class="control-label col-sm-1">上级代理商</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="parent_user" value="">
                            </div>
                            <label class="control-label col-sm-1" for="account">上级手机号</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="parent_mobile" value="">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">上级代理商编号</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="parent_code" value="">
                            </div>
                            
                            <label class="control-label col-sm-1">是否实名</label>
                            <div class="col-sm-3">
                                <?php echo Html::dropDownList('is_authentication','',[''=>'全部']+User::getAuthenticationLabels(),['class'=>'form-control']) ?>
                            </div>
                            <label class="control-label col-sm-1">状态</label>
                            <div class="col-sm-3">
                                <?php echo Html::dropDownList('status','',[''=>'全部']+User::getStatusLabels(),['class'=>'form-control']) ?>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">实名日期</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请输入注册日期" value="" name="authentication_time_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date' id="end">
                                    <input placeholder="请输入注册日期" value="" name="authentication_time_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <label class="control-label col-sm-1">注册日期</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请输入注册日期" value="" name="created_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date' id="end1">
                                    <input placeholder="请输入注册日期" value="" name="created_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">登记状态</label>
                            <div class="col-sm-3">
                                <?php echo Html::dropDownList('register','',[''=>'全部']+User::getRegisterLabels(),['class'=>'form-control']) ?>

                            </div>
                            <div class="col-sm-3" style="text-align:left;">
                                <button onclick="bootstrapTable()" type="submit" class="btn btn-primary">查 询</button>
                                <a href="<?php echo Url::toRoute('index'); ?>" class="btn btn-success">重 置</a>
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
                    <button disabled="disabled" id="btn_start" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-star" aria-hidden="true"></span> 启用
                    </button>
                    <button disabled="disabled" id="btn_stop" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-stop" aria-hidden="true"></span> 停用
                    </button>
                    <button disabled="disabled" id="btn_frozen" type="button" class="btn btn-default">
                        <span class="glyphicon glyphicon-registration-mark" aria-hidden="true"></span> 冻结收益
                    </button>
                </div>
                <table id="table" class="table table-striped table-bordered bulk_action">

                </table>
            </div>
        </div>
    </div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="stopOverhaul" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                <form id="stopForm" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>

                <button  type="button" class="btn btn-primary" onclick="stopHaulBtn()">确认</button>
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
                <form id="startForm" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>
                <button  type="button" class="btn btn-primary" onclick="startHaulBtn()">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

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
                field: 'user_code',
                title: '代理商编号',
                align: 'center',
                width:200
            },
                {
                    field: 'mobile',
                    title: '手机号',
                    align: 'center',
                    width:200
                },
                {
                    field: 'identity',
                    title: '身份证号码',
                    align: 'center',
                    width:200
                },
                {
                    field: 'bank_card',
                    title: '银行卡号',
                    align: 'center',
                    width:200
                },
                {
                    field: 'opening_bank',
                    title: '开户行',
                    align: 'center',
                    width:200
                },
                {
                    field: 'real_name',
                    title: '代理商',
                    align: 'center',
                    width:200
                },
                {
                    field: 'user_name',
                    title: '机构名称',
                    align: 'center',
                    width:200
                },
                {
                    field: 'email',
                    title: '邮箱',
                    align: 'center',
                    width:200
                },
                {
                    field: 'address',
                    title: '联系地址',
                    align: 'center',
                    width:200
                },
                {
                    field: 'parent_real_name',
                    title: '上级代理商',
                    align: 'center',
                    width:200
                },
                {
                    field: 'parent_mobile',
                    title: '上级手机号',
                    align: 'center',
                    width:200
                },
                {
                    field: 'parent_code',
                    title: '上级代理商编号',
                    align: 'center',
                    width:200
                },
                {
                    field: 'created_at',
                    title: '注册时间',
                    align: 'center',
                    colspan: 1,
                    width:200
                },
                {
                    field: 'is_authentication',
                    title: '是否实名',
                    align: 'center',
                    width:200
                },
                {
                    field: 'authentication_time',
                    title: '实名时间',
                    align: 'center',
                    width:200
                },
                {
                    field: '',
                    title: '实名证件',
                    align: 'center',
                    width:200
                },
                {
                    field: 'status',
                    title: '状态',
                    align: 'center',
                    width:200
                },
                {
                    field: 'register',
                    title: '登记状态',
                    align: 'center',
                    width:200
                },
                {
                    formatter: function (value, row, index) {
                        $str=`<div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-ellipsis-v"></i> 操作 </font></font><span class="caret"></span> </button>
                    <ul class="dropdown-menu">
                      <li><a href="javascript:son(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 查询下级</font></font></a>
                      </li>
                      <li><a href="javascript:edit_parent(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 修改上级</font></font></a>
                      </li>`;
                        if(row.settlement){
                            $str+=`<li><a href="javascript:settlementSystem(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 新增结算价设置</font></font></a>
                    		</li>`;
                        }
                        if(row.settlement){
                            $str+=`<li><a href="javascript:settlement(`+row.id+`)"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><i class="fa fa-pencil"></i> 结算价设置</font></font></a>
                    		</li>`;
                        }
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
        let startTime = $('input[name="authentication_time_start"]').val()
        let startTime1 = $('input[name="created_start"]').val()
        console.log(startTime1)
        $('#end').datetimepicker('setStartDate',startTime)
        $('#end1').datetimepicker('setStartDate',startTime1)
    });
    function son($id){
        // loading.show();
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
                //l.stop();
                //$(this).attr("disabled",false);
                // loading.hide();
            }
        });
    }

    var treeInfo = [];

    $('#myModal').on('click', '.tree', function () {

        e = $(this);
        $(this).attr("disabled",true);
        lv = parseInt(e.attr('lv'));
        id = e.data('id');
        if(e.attr('ajax') == 1){
            showTree(e, id);
            return false;
        }

        treeInfo[id] = true;

        // loading.show();
        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['son']) ?>",
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'parent_id':e.attr('data-id'),'lv':lv},
            dataType:'json',
            success:function(result){
                $px = 40 * parseInt(lv);
                if(result.length<=0){
                    e.children('.glyphicon.glyphicon-folder-open').attr('class','glyphicon glyphicon-file');
                }

                $str='';
                for(var r in result){
                    $str+=`<tr>
						<td ajax='0' lv='`+(lv+1)+`' class="tree replace_nbsp"  data-id="`+result[r]['id']+`" style="cursor:pointer; padding-left: `+$px+`px;">`+`<i class="glyphicon glyphicon-triangle-right"></i>&nbsp;&nbsp; <i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp; `+result[r]['user_name']+`</td>
	    				<td>`+result[r]['user_code']+`</td>
	    				<td>`+result[r]['mobile']+`</td>
	    				<td>`+result[r]['is_authentication']+`</td>
	    				<td>` + getUserSettlement(result[r]['userSettlement']) + `</td>
	    				<td>`+result[r]['status']+`</td>
	    			</tr>`;
                }
                e.parent().after($str);
            },
            complete : function(){
                // loading.hide();
                e.attr("disabled",false);
                e.attr('ajax','1');
                e.children('.glyphicon.glyphicon-triangle-right').attr('class','glyphicon glyphicon-triangle-bottom');

            }
        });
    });

    function getUserSettlement(data)
    {
        var result = '';
        for(var i in data)
        {
            result += data[i]['agentProductType']['productType']['name'] + '/' + data[i]['level_cc_settlement']
            + '/' + data[i]['level_dc_settlement'] + '('+ data[i]['capping'] + ')' + '/' + data[i]['cash_money'] + '</br>'
        }
        // console.log(result);return;
        return result;
    }

    function edit_parent($id){
        // loading.show();
        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['edit-parent']) ?>",
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

    //代理商信息导出
    function exports(){
        var user_code = $('input[name="user_code"]').val();
        var user_name = $('input[name="user_name"]').val();
        var mobile = $('input[name="mobile"]').val();
        var identity = $('input[name="identity"]').val();
        var parent_user = $('input[name="parent_user"]').val();
        var parent_mobile = $('input[name="parent_mobile"]').val();
        var parent_code = $('input[name="parent_code"]').val();
        var created_start = $('input[name="created_start"]').val();
        var created_end = $('input[name="created_end"]').val();
        var is_authentication = $('select[name="is_authentication"]').val();
        var authentication_time_start = $('input[name="authentication_time_start"]').val();
        var authentication_time_end = $('input[name="authentication_time_end"]').val();
        var status = $('select[name="status"]').val();
        var register = $('select[name="register"]').val();
        window.location.href = '<?php echo Url::toRoute(['export']); ?>?user_code='+user_code+'&user_name='+user_name+'&mobile='
            +mobile+'&identity='+identity+'&parent_user='+parent_user+'&parent_mobile='+parent_mobile+'&parent_code='+parent_code+
            '&created_start='+created_start+'&created_end='+created_end+'&is_authentication='+is_authentication+'&authentication_time_start='
            +authentication_time_start+'&authentication_time_end='+authentication_time_end+'&status='+status+'&register='+register;
    }

    $('#btn_start').click(function (){
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

    function startHaulBtn(){
        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['index/start']) ?>",
            data:$("#startForm").serialize(),
            dataType:'json',
            success:function(result){
                location.reload();
            }
        });
    }
	$('#btn_frozen').click(function (){
		checkedbox= $("#table").bootstrapTable('getSelections');
        $ids = new Array();
        $("#stopForm input[name='id[]']").remove();
        for(var i in checkedbox){
            $ids.push(checkedbox[i]['id']);
        }
        if($ids.length<=0){
            return false;
        }
        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['frozen']) ?>",
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':$ids},
            dataType:'json',
            success:function(result){
            	$('#myModal').html(result['html']);
                $('#myModal').modal();
            }
        });
	});
    $('#btn_stop').click(function (){

        checkedbox= $("#table").bootstrapTable('getSelections');
        // console.log(checkedbox);
        $ids = new Array();
        $("#stopForm input[name='id[]']").remove();
        for(var i in checkedbox){
            $ids.push(checkedbox[i]['id']);
            $("#stopForm").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
        }
        if($ids.length<=0){
            return false;
        }
        $("#stopOverhaul").modal({
            backdrop : 'static',
            keyboard : false
        });
    });
    function stopHaulBtn(){
        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['index/stop']) ?>",
            data:$("#stopForm").serialize(),
            dataType:'json',
            success:function(result){
                location.reload();
            }
        });
    }
    function settlement($id){
        // loading.show();
        $.ajax({
            type:"POST",
            url:"<?php echo Url::toRoute(['settlement']) ?>",
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
	function settlementSystem($id){
	    			$.ajax({
	                type:"POST",
	                url:"<?php echo Url::toRoute(['settlement-system']) ?>",
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

    function showTree(e, id){

        var lv = parseInt(e.attr('lv'));

        var isShow = treeInfo[id];

        treeInfo[id] = isShow == true ? false : true;
        // 显示中
        if(isShow){

            e.find('.glyphicon-triangle-bottom').addClass('glyphicon-triangle-right');
            e.find('.glyphicon-triangle-bottom').removeClass('glyphicon-triangle-bottom');

            // 隐藏下级
            e.parent().nextAll().each(function(index){
                var nlv = parseInt($(this).find('td').attr('lv'));
                if(nlv > lv){
                    $(this).hide();
                } else {
                    return false;
                }
            });

        //隐藏中
        } else {

            e.find('.glyphicon-triangle-right').addClass('glyphicon-triangle-bottom');
            e.find('.glyphicon-triangle-right').removeClass('glyphicon-triangle-right');

            //显示下级
            e.parent().nextAll().each(function(index){
                var slv = parseInt($(this).find('td').attr('lv'));
                if(slv > lv){
                    $(this).show();
                } else {
                    return false;
                }
            });
        }

    }
</script>