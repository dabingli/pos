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
                <span id="modalDetailTitle">商户交易记录</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form novalidate="novalidate">

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">订单号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->orderNo ?>
                    </div>

                    <label class="col-md-3 text-right control-label">商户手机号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->user->mobile ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">商户编号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->user->user_code ?>

                    </div>

                    <label class="col-md-3 text-right control-label">商户名称 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->merchantName ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">商户邮箱 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->user->email ?>

                    </div>

                    <label class="col-md-3 text-right control-label">商户注册日期 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->regDate ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">代理商 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->user->real_name ?>
                    </div>

                    <label class="col-md-3 text-right control-label">代理商编号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->merchantId ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">机具编号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->serialNo ?>

                    </div>

                    <label class="col-md-3 text-right control-label">银行卡号 :</label>
                    <div class="col-md-2 log-view">
                        -
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">交易金额 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->txAmt ?> 元
                    </div>

                    <label class="col-md-3 text-right control-label">到账金额 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->amountArrives ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">费率 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->rate ?>

                    </div>

                    <label class="col-md-3 text-right control-label">手续费 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->fee ?> 元

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">交易时间 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->txTime ?>

                    </div>

                    <label class="col-md-3 text-right control-label">付款方式 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->getType(); ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">卡类型 :</label>
                    <div class="col-md-2 log-view">
                        <?= $transaction->getCardType(); ?>

                    </div>

                    <label class="col-md-3 text-right control-label">创建日期 :</label>
                    <div class="col-md-2 log-view">
                        <?= date('Y-m-d H:i:s', $transaction->created_at); ?>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
