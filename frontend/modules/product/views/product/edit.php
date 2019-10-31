<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<script src="<?=\yii::$app->request->baseUrl?>/js/validate-1.0.0.js"></script>
<div class="modal-dialog" style="width: 55%">


    <div class="modal-content" id="detail_body">


        <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
                                                       class="glyphicon glyphicon-remove-sign"
                                                       style="font-size: 18px; cursor: pointer;"></span></b></span>
            </button>
            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">修改</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form id="form" class="form-horizontal"
                  action="<?php echo Url::toRoute(['product/edit-do']); ?>" method="post"
                  novalidate="novalidate">

                <input type="hidden" name="id" value="">
                <input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">


                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>机具编号:</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="product_no_start" />
                    </div>
                    <div class="col-md-1">
                        <label class="control-label">至</label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control required" onblur="setValue(this)" value="" type="text" name="product_no_end" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>机具台数:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入机具台数" value="" type="number"
                               class="form-control required" name="product_amount"
                               aria-required="true" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                            class="text-danger">∗</span>机具类型:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <select class="form-control required" name="type">
                            <option value="0">请选择</option>
                            <?php foreach($type as $key=>$val){ ?>
                                <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                                class="text-danger">∗</span>机具型号:</label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
                        <input placeholder="请输入机具型号" value="" type="text"
                               class="form-control required" name="model"
                               aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"><span
                                class="text-danger">∗</span>入库日期:</label>
                    <div class="col-sm-2">
                        <div class='input-group date'>
                            <input placeholder="请输入入库日期" value="<?php echo date('Y-m-d',time()); ?>" name="store_time"  type='date' class="form-control required" />
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
                            <input placeholder="请输入入库日期" value="<?php echo date('Y-m-d',time()+180*60*60*24); ?>" name="expire_time"  type='date' class="form-control required" />
                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"></label>
                    <div class="col-md-8" style="padding: 0px 10px 0px 0px;">
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
                'account': {
                    remote: {
                        url: "<?php echo Url::toRoute('product/edit-do'); ?>",     //后台处理程序
                        type: "post",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            'product_no_start': $("#form input[name='product_no_start']").val() ,
                            'product_no_end': $("#form input[name='product_no_end']").val() ,
                            'total': $("#form input[name='total']").val() ,
                            'type': $("#form input[name='type']").val() ,
                            'model': $("#form input[name='model']").val() ,
                            'store_time': $("#form input[name='store_time']").val() ,
                            'expire_time': $("#form input[name='expire_time']").val() ,
                        }
                    }
                }
            },
            onkeyup: false,
            messages: {
                'product_no_start': '请输入机具编号',
                'product_no_end': '请输入机具编号',
                'total' : '请输入台数',
                'type' : '请选择机具类型',
                'model' : '请输入机具型号',
                'store_time' : '请输入入库日期',
                'expire_time' : '请输入到期日期'
            },
            submitHandler: function (form) {
                var loading = $.loading();
                loading.show();
                $('#submit-btn').attr('disabled', true)
                //提交
                form.submit();
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

    function setValue(e)
    {
        var product_no_start_string = $('#form input[name="product_no_start"]').val()
        var product_no_end_string = $('#form input[name="product_no_end"]').val()

        var product_no_start = parseInt(product_no_start_string)
        var product_no_end = parseInt(product_no_end_string)

        if(product_no_start_string.length != product_no_end_string.length)
        {
            rfError('机具编号'+product_no_start_string+'和机具编号'+product_no_end_string+'长度不一样');
        }
        var name = $(e).attr('name')
        var product_amount = parseInt($(e).val())
        if(name == 'product_amount' && product_amount !=='' && product_no_start !== '')
        {
            var total = product_no_start + product_amount - 1;
            if(total < 0)
            {
                return false;
            }
            $(e).parents('tr').find('input[name="product_no_end[]"]').val(total)
        }

        if(product_no_start_string !== '' && product_no_end_string !== '' && name == 'product_no_end')
        {
            var total = product_no_end - product_no_start + 1
            if(total <= 0)
            {
                rfError('机具数量不能小于0');
                return false;
            }
            // console.log(total)
            $('#form input[name="product_amount"]').val(total)
        }

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
                var type = data[0].productType.name
                $(e).parents('tr').find('input[name="type[]"]').val(type)
            }
        })
    }

</script>