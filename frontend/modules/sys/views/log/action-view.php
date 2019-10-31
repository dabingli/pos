<?php
use yii\helpers\Url;
use yii\helpers\Json;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <table class="table">
        <tbody>
        <tr>
            <td>提交方法</td>
            <td><?= $model['method'] ?></td>
        </tr>
        <tr>
            <td>用户</td>
            <td><?= isset($model->manager->username) ? $model->manager->username : '游客' ?></td>
        </tr>
        <tr>
            <td>Url</td>
            <td><?= $model['url']?></td>
        </tr>
        <tr>
            <td>IP</td>
            <td><?= long2ip($model['ip'])?></td>
        </tr>
        <tr>
            <td>地区</td>
            <td><?= $model['country']; ?>·<?= $model['provinces']; ?>·<?= $model['city']; ?></td>
        </tr>

        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
</div>