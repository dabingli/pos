<?php
use yii\helpers\Url;
use yii\helpers\Json;

?>


<div class="modal-dialog" style="width:85%;">

    <div class="modal-content">
        <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
                        <span class="text-danger"><b><span id="detail_modal_close"
                                                           class="glyphicon glyphicon-remove-sign"
                                                           style="font-size: 18px; cursor: pointer;"></span></b></span>
            </button>
            <h4 class="modal-title text-danger" id="ajax-view-label">
                <span id="modalDetailTitle">基本信息</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <table class="table">
                <tbody>
                <tr>
                    <td>服务商名称</td>
                    <td><?= $model['name'] ?></td>
                </tr>
                <tr>
                    <td>服务商后台名称</td>
                    <td><?= !empty($model['admin_name']) ? $model['admin_name'] . '●' .  Yii::$app->params['title'] : '' ?></td>
                </tr>
                <tr>
                    <td>服务商编号</td>
                    <td><?= $model['number'] ?></td>
                </tr>
                <tr>
                    <td>签约日期</td>
                    <td><?= isset($model['contract_date']) ? $model['contract_date'] : '' ?></td>
                </tr>
                <tr>
                    <td>省份</td>
                    <td><?= isset($model->province->title) ? $model->province->title : '' ?></td>
                </tr>
                <tr>
                    <td>市</td>
                    <td><?= isset($model->city->title) ? $model->city->title : ''; ?></td>
                </tr>
                <tr>
                    <td>区</td>
                    <td><?= isset($model->county->title) ? $model->county->title : '' ?></td>
                </tr>
                <tr>
                    <td>联系人</td>
                    <td><?= $model['contacts']; ?></td>
                </tr>
                <tr>
                    <td>联系电话</td>
                    <td><?= $model['mobile']; ?></td>
                </tr>
                <tr>
                    <td>联系邮箱</td>
                    <td><?= $model['mailbox']; ?></td>
                </tr>
                <tr>
                    <td>联系地址</td>
                    <td><?= $model['address']; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>