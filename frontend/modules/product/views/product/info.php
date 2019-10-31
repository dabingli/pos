<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\product\Product;
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
                <span id="modalDetailTitle">机具信息</span>&nbsp;
            </h4>
        </div>

        <ul id="myTab" class="nav nav-tabs">
            <li class="active">
                <a href="#all_info" data-toggle="tab">
                    基本信息
                </a>
            </li>
            <li><a href="#send_info" data-toggle="tab">下发信息</a></li>
            <li><a href="#back_info" data-toggle="tab">回拨信息</a></li>
            <li><a href="#store_info" data-toggle="tab">入库信息</a></li>
            <li><a href="#refund_info" data-toggle="tab">退货信息</a></li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade in active" id="all_info">
                <table class="table">
                    <thead>
                    <tr>
                        <th>机具编号</th>
                        <th>机具类型</th>
                        <th>机具型号</th>
                        <th>入库日期</th>
                        <th>到期日期</th>
                        <th>代理商编号</th>
                        <th>代理商</th>
                        <th>代理商手机号</th>
                        <th>激活状态</th>
                        <th>激活时间</th>
                        <th>状态</th>
                        <th>下发时间</th>
                        <th>退货日期</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $product['info']['product_no']; ?></td>
                        <td><?php echo $product['info']['agentProductType']['productType']['name']; ?></td>
                        <td><?php echo $product['info']['model']; ?></td>
                        <td><?php echo isset($product['info']['store_time']) ? date('Y-m-d',$product['info']['store_time']) : ''; ?></td>
                        <td><?php echo isset($product['info']['expire_time']) ? date('Y-m-d',$product['info']['expire_time']) : ''; ?></td>
                        <td><?php echo $product['info']['user_code']; ?></td>
                        <td><?php echo $product['info']['user_name']; ?></td>
                        <td><?php echo $product['info']['mobile']; ?></td>
                        <td><?php echo $product['info']['ActivateStatus']; ?></td>
                        <td><?php echo !empty($product['info']['activate_time']) ? date('Y-m-d H:i:s',$product['info']['activate_time']) : ''; ?></td>
                        <td><?php echo $product['info']['status_text']; ?></td>
                        <td><?php echo !empty($product['info']['send_time']) ? date('Y-m-d H:i:s',$product['info']['send_time']) : ''; ?></td>
                        <td><?php echo !empty($product['info']['refund_time']) ? date('Y-m-d',$product['info']['refund_time']) : ''; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="send_info">
                <table class="table">
                    <thead>
                    <tr>
                        <th>下发编号</th>
                        <th>下发时间</th>
                        <th>下发人</th>
                        <th>代理商</th>
                        <th>代理商编号</th>
                        <th>代理商手机号</th>
                        <th>机具类型</th>
                        <th>机具型号</th>
                        <th>到期日期</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($product['send'])) {
                        foreach ($product['send'] as $key => $val) { ?>
                            <tr>
                                <td><?php echo $val['serial']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', $val['send_time']); ?></td>
                                <td><?php echo $val['name']; ?></td>
                                <td><?php echo $val['user_name']; ?></td>
                                <td><?php echo $val['user_code']; ?></td>
                                <td><?php echo $val['mobile']; ?></td>
                                <td><?php echo $val['type_name']; ?></td>
                                <td><?php echo $val['model']; ?></td>
                                <td><?php echo date('Y-m-d', $val['expire_time']); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="back_info">
                <table class="table">
                    <thead>
                    <tr>
                        <th>回拨编号</th>
                        <th>回拨时间</th>
                        <th>回拨人</th>
                        <th>代理商</th>
                        <th>代理商编号</th>
                        <th>代理商手机号</th>
                        <th>机具类型</th>
                        <th>机具型号</th>
                        <th>到期日期</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($product['back'])) {
                        foreach ($product['back'] as $key => $val) { ?>
                            <tr>
                                <td><?php echo $val['serial']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', $val['back_time']); ?></td>
                                <td><?php echo $val['name']; ?></td>
                                <td><?php echo $val['user_name'];?></td>
                                <td><?php echo $val['user_code'];?></td>
                                <td><?php echo $val['mobile']; ?></td>
                                <td><?php echo $val['type_name']; ?></td>
                                <td><?php echo $val['model']; ?></td>
                                <td><?php echo date('Y-m-d', $val['expire_time']); ?></td>
                             </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="store_info">
                <table class="table">
                    <thead>
                    <tr>
                        <th>入库编号</th>
                        <th>入库日期</th>
                        <th>到期日期</th>
                        <th>入库人员</th>
                        <th>机具类型</th>
                        <th>机具型号</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($product['store'])) {
                        foreach ($product['store'] as $key => $val) { ?>
                            <tr>
                                <td><?php echo $val['serial']; ?></td>
                                <td><?php echo date('Y-m-d',$val['store_time']); ?></td>
                                <td><?php echo date('Y-m-d',$val['expire_time']); ?></td>
                                <td><?php echo $val['name']; ?></td>
                                <td><?php echo $val['type_name']; ?></td>
                                <td><?php echo $val['model']; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="refund_info">
                <table class="table">
                    <thead>
                    <tr>
                        <th>退货编号</th>
                        <th>退货日期</th>
                        <th>退货人员</th>
                        <th>机具类型</th>
                        <th>机具型号</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($product['refund'])) {
                        foreach ($product['refund'] as $key => $val) { ?>
                            <tr>
                                <td><?php echo $val['serial']; ?></td>
                                <td><?php echo date('Y-m-d',$val['refund_time']); ?></td>
                                <td><?php echo $val['name']; ?></td>
                                <td><?php echo $val['type_name']; ?></td>
                                <td><?php echo $val['model']; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>