<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\RegionWidget;
use backend\assets\AppAsset;
use common\services\Agent;
use common\models\product\Product;
use yii\widgets\ActiveForm;

$this->title = '机具类型';
$this->params['breadcrumbs'][] = '基础信息';
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
                    <form id="myForm" onsubmit="return bootstrapTable($(this))" action="" method="get" class="form-horizontal">
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1" for="model">机具型号</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="model" value="" id="model">
                            </div>

                            <label class="control-label col-sm-1" for="user_name">代理商</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="user_name" value="" id="user_name">
                                <span style="color:red;">*查询下级需要填写完整</span>
                            </div>
                            <label class="control-label col-sm-1">激活状态</label>
                            <div class="col-sm-3">
                                <?php echo Html::dropDownList('activate_status','',[''=>'全部']+Product::ActivateStatusLabels(),['class'=>'form-control']) ?>

                            </div>
                            
                        </div>
                        <div class="form-group" style="margin-top:15px">
                        </div>
                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">到期日期</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请输入到期日期" value="" name="expire_time_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date' id="end">
                                    <input placeholder="请输入到期日期" value="" name="expire_time_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <label class="control-label col-sm-1">激活日期</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请输入激活日期" value="" name="activate_time_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date' id="end1">
                                    <input placeholder="请输入激活日期" value="" name="activate_time_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>  
                        </div>

                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">下发时间</label>
                            <div class="col-sm-2">
                                <div class='input-group date'>
                                    <input placeholder="请输入下发时间" value="" name="send_time_start"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class='input-group date' id="end2">
                                    <input placeholder="请输入下发时间" value="" name="send_time_end"  type='date' class="form-control" />
                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                            <label class="control-label col-sm-1">机具编号</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" name="product_no_start" value="" id="product_no_start">
                            </div>
                            <div class="col-sm-1" style="width:3%;">
                                <label class="control-label">至</label>
                            </div>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" name="product_no_end" value="" id="product_no_end">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:15px">
                            <label class="control-label col-sm-1">状态</label>
                            <div class="col-sm-2">
                                <?php
                                    $status_list = Product::StatusLabels();
                                    unset($status_list[Product::NO_SEND]);
                                    echo Html::dropDownList('status','',[''=>'全部']+$status_list,['class'=>'form-control'])
                                ?>
                            </div>
                            <label class="control-label col-sm-1">是否查询下级</label>
                            <div class="col-sm-2">
                                <select name="is_search_children" class="form-control">
                                    <option value="1">否</option>
                                    <option value="2">是</option>
                                </select>
                            </div>
                            <div class="col-sm-4" style="text-align:left;">
                                <button type="submit" class="btn btn-primary">查 询</button>
                                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                                <a href="<?php echo Url::toRoute(['/product/product/index']); ?>" class="btn btn-success">重 置</a>
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
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 入库
                        </button>
                        <button id="refund" type="button" class="btn btn-default">
                            退货
                        </button>
                        <button id="edit" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-edit"></span>
                            修改
                        </button>
                        <button id="send" type="button" class="btn btn-default">
                            下发
                        </button>
                        <button id="back" type="button" class="btn btn-default">
                            回拨
                        </button>
                        <button disabled id="frozen_start" type="button" class="btn btn-default">
                            冻结
                        </button>
                        <button disabled id="frozen_stop" type="button" class="btn btn-default">
                            取消冻结
                        </button>
                        <button onclick="exports()" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-cloud-download"></span>
                            导出
                        </button>
                        <button id="again-store" type="button" class="btn btn-default">
                            重新入库
                        </button>
                        <!--<button type="button" class="btn btn-default" data-toggle="modal" data-target="#importModal" id="import">
                            <span class="glyphicon glyphicon-cloud-upload"></span>
                            导入
                        </button>-->
                    </div>
                    <table id="table" class="table table-striped table-bordered bulk_action">

                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

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
                你确定需要取消冻结吗？
            </div>
            <div class="modal-footer">
                <form id="modalFrom" style="display:none">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                </form>

                <button  type="button" class="btn btn-primary" onclick="frozenHaulBtn()">确认</button>
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
                你确定需要冻结吗？
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

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form id="form" class="form-horizontal"
              action="<?php echo Url::toRoute(['product/import']); ?>" enctype="multipart/form-data" method="post"
              novalidate="novalidate">
            <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        导入Excell
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <div class="col-sm-9">
                                    <input type="file" name="Product[file]"/>
                                    <span class="Validform_checktip"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id='formbtn'  class="btn btn-primary">
                        提交保存
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        关闭
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
        </form>
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
                    field: 'product_no',
                    title: '机具编号',
                    formatter:function(value,row,index){
                        var s;
                        if(row.product_no!=null){
                            s = '<a onclick="info(this)" data-value="'+row.id+'">'+row.product_no+'</a>';
                        }
                        return s;

                    }
                },{
                    field: 'type',
                    title: '机具类型'
                },{
                    field: 'model',
                    title: '机具型号'
                },{
                    field: 'store_time',
                    title: '入库日期'
                },{
                    field: 'expire_time',
                    title: '到期日期'
                },{
                    field: 'user_code',
                    title: '代理商编号'
                },{
                    field: 'user_name',
                    title: '代理商'
                },{
                    field: 'activate_time',
                    title: '激活时间'
                },{
                    field: 'activate_status',
                    title: '激活状态'
                },{
                    field: 'frost_status',
                    title: '冻结状态'
                }, {
                        field: 'status',
                        title: '状态'
                    }, {
                        field: 'send_time',
                        title: '下发时间'
                    },
                    {
                        field: 'refund_time',
                        title: '退货日期'
                    },
                ]
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
            // console.log(params);
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
            let startTime = $('input[name="expire_time_start"]').val()
            let startTime1 = $('input[name="activate_time_start"]').val()
            let startTime2 = $('input[name="send_time_start"]').val()
            $('#end').datetimepicker('setStartDate',startTime)
            $('#end1').datetimepicker('setStartDate',startTime1)
            $('#end2').datetimepicker('setStartDate',startTime2)
        });


        $("#btn_add").click(function(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['store']) ?>",
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
                $('#frozen_start').attr("disabled",false);
                $('#frozen_stop').attr("disabled",false);
                //$('#btn_stop').attr("disabled",false);
                //$('#btn_delete').attr("disabled",false);
            }else{
                $('#frozen_start').attr("disabled",true);
                $('#frozen_stop').attr("disabled",true);
                ///$('#btn_stop').attr("disabled",true);
                //$('#btn_delete').attr("disabled",true);
            }
        }

        //退货
        $('#refund').click(function(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['refund']) ?>",
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
        })

        $('#edit').click(function(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['edit']) ?>",
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
        })

        //回拨
        $('#back').click(function(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['back']) ?>",
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
        })

        //    下发
        $('#send').click(function(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['send']) ?>",
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
        })

        function info(e)
        {
            var id = $(e).data('value');
            // var l = Ladda.create(this);
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['info']) ?>",
                data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':id},
                dataType:'json',
                success:function(result){
                    $('#add').html(result['html']);
                    $('#add').modal();
                },
                beforeSend : function(){
                    // l.start();
                    // $(e).attr("disabled","true");
                },
                complete : function(){
                    // l.stop();
                    // $(e).attr("disabled",false);
                    // loading.hide();
                }
            });
        }

        //    导出
        function exports(){
            var model = $('#model').val();
            var user_name = $('#user_name').val();
            var expire_time_start = $('input[name="expire_time_start"]').val();
            var expire_time_end = $('input[name="expire_time_end"]').val();
            var activate_time_start = $('input[name="activate_time_start"]').val();
            var activate_time_end = $('input[name="activate_time_end"]').val();
            var product_no_start = $('input[name="product_no_start"]').val();
            var product_no_end = $('input[name="product_no_end"]').val();
            var activate_status = $('select[name="activate_status"]').val();
            var status = $('select[name="status"]').val();
            var send_time_start = $('input[name="send_time_start"]').val();
            var send_time_end = $('input[name="send_time_end"]').val();
            var is_search_children = $('select[name="is_search_children"]').val()

            if(is_search_children == null)
            {
                is_search_children = 1;
            }

            // console.log($('select[name="status"]').val())
            window.location.href = '<?php echo Url::toRoute(['product/export']); ?>?model='+model+'&user_name='+user_name+
                '&expire_time_start='+expire_time_start+'&expire_time_end='+expire_time_end+'&activate_time_start='+activate_time_start+
                '&activate_time_end='+activate_time_end+'&product_no_start='+product_no_start+'&product_no_end='+product_no_end+'&activate_status='+activate_status+
                '&status='+status+'&send_time_start='+send_time_start+'&send_time_end='+send_time_end + '&is_search_children=' + is_search_children
        }

        //退货
        $('#again-store').click(function(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['again-store']) ?>",
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
        $('#frozen_start').click(function (){
            checkedbox= $("#table").bootstrapTable('getSelections');
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
        $('#frozen_stop').click(function (){
            checkedbox= $("#table").bootstrapTable('getSelections');
            $ids = new Array();
            $("#modalFrom input[name='id[]']").remove();
            for(var i in checkedbox){
                $ids.push(checkedbox[i]['id']);
                $("#modalFrom").append('<input type="hidden" name="id[]" value="'+checkedbox[i]['id']+'" />');
            }
            if($ids.length<=0){
                return false;
            }
            $("#stopOverhaul").modal();
        });
        function frozenHaulBtn(){
            $.ajax({
                type:"POST",
                async:true,//false时为同步true为异步一般是异步
                url:"<?php echo Url::toRoute(['frozen']) ?>",
                data:$("#modalFrom").serialize(),
                dataType:'json',
                success:function(result){
                    location.reload();
                }
            });
        }
    </script>