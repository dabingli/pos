<?php


?>
<style>
    .log-view{
        padding: 0px 8px 0px 0px;
        margin-top:7px;
    }
</style>

<div class="modal-dialog" style="width: 80%">

    <div class="modal-content" id="detail_body">


        <div class="modal-header text-center">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <span class="text-danger">
                    <b>
                        <span id="detail_modal_close" class="glyphicon glyphicon-remove-sign" style="font-size: 18px; cursor: pointer;"></span>
                    </b>
                </span>
            </button>

            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">收益记录</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form novalidate="novalidate">

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">订单号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->order ?>
                    </div>

                    <label class="col-md-3 text-right control-label">商户手机号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->user->mobile ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">商户编号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->user->user_code ?>

                    </div>

                    <label class="col-md-3 text-right control-label">商户名称 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->merchantName ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">商户邮箱 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->user->email ?>

                    </div>

                    <label class="col-md-3 text-right control-label">商户注册日期 :</label>
                    <div class="col-md-2 log-view">
                        <?= date('Y-m-d', $detail->user->register_time) ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">代理商 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->user->real_name ?>
                    </div>

                    <label class="col-md-3 text-right control-label">代理商编号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->merchantId ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">机具编号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->serialNo ?>

                    </div>

                    <label class="col-md-3 text-right control-label">是否入账 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->getEntry() ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">交易金额 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->transaction_amount ?> 元
                    </div>

                    <label class="col-md-3 text-right control-label">收益金额 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->amount_profit ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">收益类型 :</label>
                    <div class="col-md-2 log-view">
                        <?= $detail->getType() ?>

                    </div>

                    <label class="col-md-3 text-right control-label">收益时间 :</label>
                    <div class="col-md-2 log-view">
                        <?= date('Y-m-d H:i:s', $detail->created_at) ?>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
