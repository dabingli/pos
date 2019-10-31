<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;

AppAsset::register($this);
?>

<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<link href="<?= \yii::$app->request->baseUrl . "/select2/dist/css/select2.min.css"?>" rel="stylesheet" />
<script src="<?=\yii::$app->request->baseUrl?>/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/select2/dist/js/select2.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?=\yii::$app->request->baseUrl?>/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 13px;
        color:#444;
    }
</style>
<div class="modal-dialog" style="width: 85%">


    <div class="modal-content" id="detail_body">


        <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
                                                       class="glyphicon glyphicon-remove-sign"
                                                       style="font-size: 18px; cursor: pointer;"></span></b></span>
            </button>
            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">下发</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['product/send-add']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="">
                <input type="hidden" name="user_id" value="">
                <input type="hidden" name="user_name" value="">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>下发编号:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入下发编号" value="<?php echo $number; ?>" type="text"
                               class="form-control required" name="serial"
                               aria-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><span
                            class="text-danger">∗</span>代理商:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <?php echo Html::dropDownList('user','',$user,['class'=>'prompt','prompt' => '请选择','id'=>'sel_menu2','onchange'=>'searchUser(this)','style'=>'width:100%;']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                                class="text-danger">∗</span>代理商编号:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入代理商编号" value="" type="text"
                               class="form-control required" name="user_code"
                               aria-required="true" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                                class="text-danger">∗</span>代理商手机号:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入代理商手机号" value="" type="text"
                               class="form-control required" name="mobile"
                               aria-required="true" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <table class="table table-striped table-bordered bulk_action table-hover">
                        <h4>机具信息</h4>
                        <thead>
                        <tr>
                            <th>机具编号</th>
                            <th>机具台数</th>
                            <th>机具类型</th>
                            <th>机具型号</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr id="tr">
                            <td>
                                <div class="form-group">
                                    <div class="col-md-4" style="width:45%;">
                                        <input type="text" onblur="searchType(this)" class="form-control" min="0" required name="product_no_start[]" />
                                    </div>
                                    <div class="col-md-1">
                                        <label class="control-label">至</label>
                                    </div>
                                    <div class="col-md-4" style="width:45%;">
                                        <input class="form-control" onblur="setValue(this)" min="0" required type="text" name="product_no_end[]" />
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="col-md-8" style="width:100%;">
                                        <input type="number" class="form-control" min="1" required name="product_amount[]" readonly>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="col-md-8" style="width:100%;">
                                        <input type="text" class="form-control" name="type[]" required readonly>
                                        <input type="hidden" class="form-control" name="agent_product_type_id[]">
                                    </div>
                                </div>
                            </td>
                            <td><input type="text" class="form-control" name="model[]" required readonly></td>
                            <td style="width:10%"><span onclick="add(this)" class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-remove" onclick="remove(this)"></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"></label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 20%;">
                        <button  type="submit" class="btn btn-primary" id="submit-btn">提&nbsp;&nbsp;交</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    function validate(){
        $("#form").validate({
            ignore: [],
            rules: {

            },
            onkeyup: false,
            messages: {
                'user_name': '请填写代理商',
                'user_code' : '请填写代理商编号',
                'mobile' : '请填写代理商手机号',
                'product_no_start[]' :{
                    required : '请填写开始机具编号',
                    min: '不能为负数'
                },
                'product_no_end[]' :{
                    required : '请填写结束机具编号',
                    min: '不能为负数'
                },
                'product_amount[]' :{
                    required : '请填写机具数量',
                    min: '必须大于0'
                },
                'type[]' :{
                    required: '机具类型不能为空',
                },
                'model[]' : {
                    required : '机具型号不能为空'
                }
            },
            submitHandler: function (form) {
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
            }
        });
    }
    validate();

    function add(e)
    {

        var html = '';
        html += '<tr>'
        html += '<td>\n' +
            '                            <div class="form-group">\n' +
            '                                <div class="col-md-4" style="width:45%;">\n' +
            '                                    <input type="text" onblur="searchType(this)" min="0" required class="form-control" name="product_no_start[]" />\n' +
            '                                </div>\n' +
            '                                <div class="col-md-1">\n' +
            '                                    <label class="control-label">至</label>\n' +
            '                                </div>\n' +
            '                                <div class="col-md-4" style="width:45%;">\n' +
            '                                    <input class="form-control" onblur="setValue(this)" min="0" required type="text" name="product_no_end[]" />\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '                        </td>';
        html += '<td>\n' +
            '                            <div class="form-group">\n' +
            '                                <div class="col-md-8" style="width:100%;">\n' +
            '                                    <input type="text" class="form-control" min="1" required name="product_amount[]" readonly>\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '                        </td>'
        html += '<td>'
        html +=     '<div class="form-group">'
        html +=           '<div class="col-md-8" style="width:100%;">'
        html +=                 '<input type="text" class="form-control" name="type[]" required readonly>'
        html +=                 '<input type="hidden" class="form-control" name="agent_product_type_id[]" readonly>'
        html +=            '</div>'
        html +=      '</div>'
        html += '</td>'
        html += '<td><input type="text" class="form-control" name="model[]" required readonly></td>'
        html += '<td style="width:10%"><span onclick="add(this)" class="glyphicon glyphicon-plus"></span><span onclick="remove(this)" class="glyphicon glyphicon-remove"></span></td>'
        html += '</tr>'
        // console.log
        $(e).parents('tbody').append(html);
    }

    function remove(e)
    {
        var len = $(e).parents('tbody').children('tr').length;
        if(len == 1)
        {
            return false;
        }
        $(e).parents('tr').remove();
    }

    function setValue(e)
    {
        var product_no_start_string = $(e).parents('tr').find('input[name="product_no_start[]"]').val()
        var product_no_end_string = $(e).parents('tr').find('input[name="product_no_end[]"]').val()

        var product_no_start = parseInt(product_no_start_string)
        var product_no_end = parseInt(product_no_end_string)

        if(product_no_start_string.length != product_no_end_string.length)
        {
            rfError('机具编号'+product_no_start_string+'和机具编号'+product_no_end_string+'长度不一样');
        }
        var name = $(e).attr('name')
        var product_amount = parseInt($(e).val())
        if(name == 'product_amount[]' && product_amount !=='' && product_no_start !== '')
        {
            var total = product_no_start + product_amount - 1;
            if(total < 0)
            {
                return false;
            }
            $(e).parents('tr').find('input[name="product_no_end[]"]').val(total)
        }

        if(product_no_start_string !== '' && product_no_start_string !== '' && name == 'product_no_end[]')
        {
            var total = product_no_end - product_no_start + 1
            if(total <= 0)
            {
                rfError('数量不能小于0')
                return false;
            }
            // console.log(total)
            $(e).parents('tr').find('input[name="product_amount[]"]').val(total)
        }

    }

    function searchUser(e)
    {
        var id = $(e).val()
        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['search-user']) ?>",
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'id':id},
            dataType:'json',
            success:function(data){
                // if(data.code < 0){
                //     showTips(data.msg)
                // }
                $('input[name="mobile"]').val(data.data.mobile)
                $('input[name="user_code"]').val(data.data.user_code)
                $('input[name="user_id"]').val(data.data.id)
                $('input[name="user_name"]').val($(e).find("option:selected").text())
            }
        })
    }

    function searchType(e)
    {
        var product_no = $(e).val()
        if(product_no == '')
        {
            return false;
        }
        $.ajax({
            type:"POST",
            async:true,//false时为同步true为异步一般是异步
            url:"<?php echo Url::toRoute(['search-type']) ?>",
            data:{'<?= \Yii::$app->request->csrfParam?>':$("[name='csrf-token']").attr('content'),'product_no':product_no},
            dataType:'json',
            success:function(data){
                var type = data[0].agentProductType.productType.name
                var model = data[0].model;
                var agent_product_type_id = data[0].agentProductType.id;
                $(e).parents('tr').find('input[name="type[]"]').val(type)
                $(e).parents('tr').find('input[name="model[]"]').val(model)
                $(e).parents('tr').find('input[name="agent_product_type_id[]"]').val(agent_product_type_id)
            }
        })
    }

    $("#sel_menu2").select2({
        tags: true,
        language: 'zh-CN',
    });

</script>