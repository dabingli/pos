<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\helpers\ArrayHelper;
use common\models\agent\AgentUser;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute(['ajax-edit','id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'account')->textInput() ?>
        <?= $form->field($model, 'user_name')->textInput() ?>
        <?= $form->field($model, 'number')->textInput() ?>
        <?= $form->field($model, 'mobile')->textInput() ?>
        <?= $form->field($model, 'mailbox')->textInput() ?>
        <?= $form->field($model, 'password')->passwordInput(['value'=>'']) ?>
        <?php if($model->root != AgentUser::ROOT){?>
            <?= $form->field($model, 'auth_key')->dropDownList(ArrayHelper::map($roles, 'key', 'name')) ?>
        <?php } ?>
        <?= $form->field($model, 'remarks')->textarea() ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>