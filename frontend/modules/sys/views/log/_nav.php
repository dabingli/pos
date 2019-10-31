<?php
use yii\helpers\Url;
?>

<ul class="nav nav-tabs">
    <li <?php if ($type == 'action'){ ?>class="active"<?php } ?>><a href="<?= Url::to(['action'])?>"> 行为日志</a></li>
</ul>