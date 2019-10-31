<?php

use backend\assets\AppAsset;

$this->title = '收益汇总';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

?>
<style>
    .log-view{
        padding: 0px 8px 0px 0px;
        margin-top:7px;
    }
</style>

<div class="modal-dialog" style="width: auto">

    <div class="modal-content" id="detail_body">


        <div class="modal-header text-center">

            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">收益汇总</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form novalidate="novalidate">

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">总收益(平台) :</label>
                    <div class="col-md-8 log-view">
                        <?= round($platformReturnProfit+$platformProfit, 2) ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">返现收益(平台) :</label>
                    <div class="col-md-2 log-view">
                        <?= round($platformReturnProfit, 2) ?> 元

                    </div>

                    <label class="col-md-3 text-right control-label">分润收益(平台) :</label>
                    <div class="col-md-2 log-view">
                        <?= round($platformProfit, 2) ?> 元

                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">总收益(代理商) :</label>
                    <div class="col-md-8 log-view">
                        <?= round($returnProfit+$profit, 2) ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">返现收益(代理商) :</label>
                    <div class="col-md-2 log-view">
                        <?= round($returnProfit, 2) ?> 元

                    </div>

                    <label class="col-md-3 text-right control-label">分润收益(代理商) :</label>
                    <div class="col-md-2 log-view">
                        <?= round($profit, 2) ?> 元

                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">总已提现金额(代理商) :</label>
                    <div class="col-md-8 log-view">
                        <?= round($cashReturnProfit+$cashProfit, 2) ?> 元
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">返现已提现金额(代理商) :</label>
                    <div class="col-md-2 log-view">
                        <?= round($cashReturnProfit, 2) ?> 元

                    </div>

                    <label class="col-md-3 text-right control-label">分润已提现金额(代理商) :</label>
                    <div class="col-md-2 log-view">
                        <?= round($cashProfit, 2) ?> 元

                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">总未提现金额(代理商) :</label>
                    <div class="col-md-8 log-view">
                        <?= round(($returnProfit+$profit) - ($cashReturnProfit+$cashProfit), 2) ?> 元

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">返现未提现金额(代理商) :</label>
                    <div class="col-md-2 log-view">
                        <?= round(($returnProfit - $cashReturnProfit), 2) ?> 元

                    </div>

                    <label class="col-md-3 text-right control-label">分润未提现金额(代理商) :</label>
                    <div class="col-md-2 log-view">
                        <?= round(($profit - $cashProfit), 2) ?> 元

                    </div>
                </div>

                <hr>

            </form>
        </div>
    </div>
</div>
