<?php
namespace app\components\captcha;

use Yii;
use yii\validators\Validator;
use yii\base\InvalidConfigException;

class CaptchaValidator extends \app\components\captcha\BaseValidator
{

    public $captchaAction = 'site/captcha';

    public $skipOnEmpty = false;

    public $caseSensitive = false;

    protected function validateValue($value)
    {
        $captcha = $this->createCaptchaAction();
        $valid = ! is_array($value) && $captcha->validate($value, $this->caseSensitive);
        return $valid ? null : [
            $this->message,
            []
        ];
    }

    public function createCaptchaAction()
    {
        $ca = Yii::$app->createController($this->captchaAction);
        if ($ca !== false) {
            /* @var $controller \yii\base\Controller */
            list ($controller, $actionID) = $ca;
            $action = $controller->createAction($actionID);
            if ($action !== null) {
                return $action;
            }
        }
        throw new InvalidConfigException('Invalid CAPTCHA action ID: ' . $this->captchaAction);
    }
}
