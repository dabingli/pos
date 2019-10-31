<?php
namespace frontend\controllers;

use Yii;
use yii\filters\HttpCache;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use frontend\models\LoginForm;
use common\models\common\Provinces as Region;
use common\library\changjie\ReceiveOrder;

/**
 * 站点控制器
 *
 * Class SiteController
 *
 * @package backend\controllers
 */
class SiteController extends Controller
{

    /**
     * 默认布局文件
     *
     * @var string
     */
    public $layout = "login";

    /**
     * 独立动作
     *
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            // 验证码
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'maxLength' => 6, // 最大显示个数
                'minLength' => 4, // 最少显示个数
                'padding' => 5, // 间距
                'height' => 32, // 高度
                'width' => 100, // 宽度
                'offset' => 1, // 设置字符偏移量
                'backColor' => 0xffffff, // 背景颜色
                'foreColor' => 0x1ab394 // 字体颜色
            ]
        ];
    }

    /**
     * 行为控制
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'login',
                            'error',
                            'captcha'
                        ],
                        'allow' => true,
                        'roles' => [
                            '?'
                        ] // 游客
                    ],
                    [
                        'allow' => true,
                        'roles' => [
                            '@'
                        ] // 登录
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => [
                        'post'
                    ]
                ]
            ]
        ];
    }

    /**
     * 登录
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        if (! Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        $model->loginCaptchaRequired();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }
        
        return $this->render('login', [
            'model' => $model
        ]);
    }

    /**
     * 退出登陆
     *
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        
        return $this->goHome();
    }

    public function actionRegion()
    {
        $model = Region::find();
        $model->andWhere([
            'pid' => Yii::$app->request->get('region_id')
        ]);
        return $this->asJson([
            'data' => $model->all()
        ]);
    }
    
    // public function actionTest()
    // {
    // $model = new ReceiveOrder();
    // $model->load([
    // 'OutTradeNo' => time(),
    // 'OriOutTradeNo' => '1907011143382703'
    // ], '');
    // $http = $model->http();
    // $content = $http->send()->content;
    // $content = json_decode($content, true);
    //
    // print_r($content);die;
    //
    // }
}
