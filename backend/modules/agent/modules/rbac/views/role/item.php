<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\RbacItem;
?>
<?php echo RbacItem::widget(['id'=>Yii::$app->request->post('id')]); ?>