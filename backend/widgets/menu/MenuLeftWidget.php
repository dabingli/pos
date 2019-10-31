<?php
namespace backend\widgets\menu;

use Yii;
use yii\base\Widget;
use common\enums\StatusEnum;
use common\models\sys\Menu;

/**
 * 左边菜单
 *
 * Class MenuLeftWidget
 */
class MenuLeftWidget extends Widget
{

    /**
     *
     * @return string
     */
    public function run()
    {
        return $this->render('menu-left', [
            'menus' => Menu::getList(StatusEnum::ENABLED)
        ]);
    }
}