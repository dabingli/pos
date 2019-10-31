<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
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
                <span id="modalDetailTitle">入库</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['product/store-add']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>入库编号:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入入库编号" value="<?php echo $number; ?>" type="text"
                               class="form-control required" name="serial"
                               aria-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>入库日期:</label>
                    <div class="col-sm-2">
                        <div class='input-group date'>
                            <input placeholder="请输入入库日期" onchange="setDate(this)" value="<?php echo date('Y-m-d',time()); ?>" name="store_time"  type='date' class="form-control" />
                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>到期日期:</label>
                    <div class="col-sm-2">
                        <div class='input-group date'>
                            <input placeholder="请输入入库日期" onchange="setDate(this)" value="<?php echo date('Y-m-d',time()+180*60*60*24); ?>" name="expire_time"  type='date' class="form-control" />
                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>入库人员:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入入库人员" value="" type="text"
                               class="form-control required" name="name"
                               aria-required="true">
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
                                    <input type="text" class="form-control" min="0" required name="product_no_start[]" />
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
                                <div class="col-md-12" style="width:100%;">
                                    <select class="form-control" name="type[]" required>
                                        <option value="">请选择</option>
                                        <?php foreach($type as $key=>$val){ ?>
                                            <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td><input type="text" class="form-control" name="model[]" required></td>
                        <td style="width:10%"><span onclick="add(this)" class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-remove"></span></td>
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
                'name': '请填写入库人员',
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
                    required : '请选择机具类型',
                },
                'model[]' :{
                    required : '请填写型号',
                },
            },

            submitHandler: function (form) {
                // $('#submit-btn').attr('disabled', true)
                //提交
                // form.submit();
                $.ajax({
                         url : '<?php echo Url::toRoute(['product/store-add']); ?>',
                         type : 'post',
                         data : $('#form').serialize(),
                         success : function(data) {
                             if(data.status==1){
                                 console.log(data.status);
                                    rfSuccess(data.msg);
                                 window.location.reload();
                             }else{
                                 rfError(data.msg);
                             }
                         }
                 });
            }
        });

    }
    validate();

    $('.date').datetimepicker({
        language: 'zh-CN',
        minView: 4,
        autoclose: true,
        format : 'yyyy-mm-dd'
    });

    function add(e)
    {

        var html = '';
        html += '<tr>'
        html += '<td>\n' +
            '                            <div class="form-group">\n' +
            '                                <div class="col-md-4" style="width:45%;">\n' +
            '                                    <input type="text" class="form-control" min="0" required name="product_no_start[]" />\n' +
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
        html +=           '<div class="col-md-12" style="width:100%;">'
        html +=                 selection()
        html +=            '</div>'
        html +=      '</div>'
        html += '</td>'
        html += '<td><input type="text" class="form-control" name="model[]" required></td>'
        html += '<td style="width:10%"><span onclick="add(this)" class="glyphicon glyphicon-plus"></span><span onclick="remove(this)" class="glyphicon glyphicon-remove"></span></td>'
        html += '</tr>'
        // console.log
        $(e).parents('tbody').append(html);
    }

    function selection()
    {
        var type = <?php echo json_encode($type); ?>;
        var len = type.length;
        // console.log(type[0])
        var html = ''
             html += '<select class="form-control" name="type[]" required>'
             html += '<option value="">请选择</option>'
        for(var i=0;i<len;i++)
        {
            html += '<option value="'+type[i]["id"]+'">'+ type[i]["name"] + '</option>'
        }
        html += '</select>';
        return html;
    }

    function remove(e)
    {
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
        // console.log(product_no_end)
        if(product_no_start_string !== '' && product_no_end_string !== '' && name == 'product_no_end[]')
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

    function setDate(e){
        var name = $(e).attr('name');
        if(name == 'store_time')
        {
            var store_time_date = $(e).val();
            let store_time = new Date(store_time_date).getTime()
            let expire_time = getTimeByDay(store_time,180) //获取30天后的日期
            let lastTimeDate = formatTime(expire_time)
            $('input[name="expire_time"]').val(lastTimeDate)
        }else{
            var expire_time_date = $(e).val();
            let expire_time = new Date(expire_time_date).getTime()
            let store_time = getTimeByDay(expire_time,-180) //获取30天后的日期
            let lastTimeDate = formatTime(store_time)
            $('input[name="store_time"]').val(lastTimeDate)
        }
    }

    /*
    num 获取当天多少天后的日期
    */
    function getTimeByDay(today,num) {
        return today + 60 * 60 * 1000 * 24 * num;
    }

    function formatTime(time) {
        //new Date(time).toISOString()    => 2019-02-23T08:40:35.825Z
        return new Date(time).toISOString().split('T')[0];
    }

</script>