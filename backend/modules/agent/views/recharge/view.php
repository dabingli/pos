<?php

    use common\models\agent\AgentRechargeLog;

?>
<style>
    .log-view{
        padding: 0px 8px 0px 0px;
        margin-top:7px;
    }
</style>

<div class="modal-dialog">

    <div class="modal-content" id="detail_body">


        <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
                                                       class="glyphicon glyphicon-remove-sign"
                                                       style="font-size: 18px; cursor: pointer;"></span></b></span>
            </button>
            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">充值记录详情</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form novalidate="novalidate">

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">订单号:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['recharge_no'] ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">代理商:</label>
                    <div class="col-md-8 log-view">
                        <?= $agent['name'] ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">代理商编号:</label>
                    <div class="col-md-8 log-view">
                        <?= $agent['number'] ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">代理商手机号码:</label>
                    <div class="col-md-8 log-view">
                        <?= $agent['mobile'] ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">标题:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['title'] ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">充值类型:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog->getType() ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">充值金额:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['money'] ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">充值短信:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['sms_number'] ?> 条
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">到账金额:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['real_money'] ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">手续费:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['fee'] ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">原剩余代付金:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['old_money'] ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">充值后剩余代付金:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['new_money'] ?> 元
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 text-right control-label">原剩余短信条数:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['old_sms_number'] ?> 条
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">充值后剩余短信条数:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['new_sms_number'] ?> 条
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">充值状态:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog->getStatus() ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">充值时间:</label>
                    <div class="col-md-8 log-view">
                        <?= date('Y-m-d H:i:s', $rechargeLog['created_at']) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">到账时间:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['pay_at'] ? date('Y-m-d H:i:s', $rechargeLog['pay_at']) : 0 ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right control-label">描述:</label>
                    <div class="col-md-8 log-view">
                        <?= $rechargeLog['content'] ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
