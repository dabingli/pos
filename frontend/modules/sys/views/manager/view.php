<?php

use common\models\common\Provinces;

$provinces = new Provinces();

?>
<style>
    .log-view{
        padding: 0px 8px 0px 0px;
        margin-top:7px;
    }
    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
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
                <span id="modalDetailTitle">用户详情</span>&nbsp;
            </h4>
        </div>

        <div class="modal-body form-horizontal">
            <form novalidate="novalidate">

                <div class="form-group">
                    <label class="col-md-3 text-right control-label"></label>
                    <div class="col-md-2 log-view">
                        <img class="avatar" src="<?= $model->head_portrait ? $model->head_portrait : '/new_pos/pos/frontend/web/resources/dist/img/profile_small.jpg' ?>">
                    </div>

                    <label class="col-md-3 text-right control-label">登录账号 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->account ?>
                    </div>
                    <br><br>

                    <label class="col-md-3 text-right control-label">用户名 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->user_name ?>
                    </div>
                    <br><br>

                    <label class="col-md-3 text-right control-label">手机号码 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->mobile ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">性别 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->getGender() ?>
                    </div>

                    <label class="col-md-3 text-right control-label">超级管理员 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->getRoot() ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">最后登录ip :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->login_IP ?>

                    </div>

                    <label class="col-md-3 text-right control-label">最后登录时间 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->last_time ? date('Y-m-d H:i:s', $model->last_time) : '-' ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">所在区域 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->provinces ? $provinces->getCityName($model->provinces) : '' ?>
                        <?= $model->city ? $provinces->getCityName($model->city) : '' ?>
                        <?= $model->area ? $provinces->getCityName($model->area) : '' ?>
                    </div>

                    <label class="col-md-3 text-right control-label">出生日期 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->birthday ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">详细地址 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->address ?>
                    </div>

                    <label class="col-md-3 text-right control-label">联系邮箱 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->mailbox ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 text-right control-label">状态 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->getStatus() ?>
                    </div>

                    <label class="col-md-3 text-right control-label">备注 :</label>
                    <div class="col-md-2 log-view">
                        <?= $model->remarks ?>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
