<?php
namespace backend\controllers;

use common\models\entities\app\AppAdvertise;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\LoginForm;
use common\models\common\Provinces as Region;
use yii\web\UploadedFile;
use common\library\oss\Aliyunoss;

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
                'minLength' => 6, // 最少显示个数
                'padding' => 5, // 间距
                'height' => 32, // 高度
                'width' => 100, // 宽度
                'offset' => 4, // 设置字符偏移量
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
            // 记录行为日志
            Yii::$app->services->sys->log('login', '自动登录', false);
            return $this->goHome();
        }
        
        $model = new LoginForm();
        $model->loginCaptchaRequired();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // 记录行为日志
            Yii::$app->services->sys->log('login', '账号密码登录', false);
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
        // 记录行为日志
        Yii::$app->services->sys->log('logout', '退出登录');
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

    // 上传
    public function actionUpload()
    {
        $m = 'image';
        $model = new AppAdvertise();
        $uploadedFile = UploadedFile::getInstance($model, $m);
        if ($uploadedFile) {
            $rootPath = Yii::getAlias('@public');
            $pathDir = 'image/' . date('Ymd');
            if (! is_dir($rootPath . '/' . $pathDir)) {
                // var_dump($rootPath .'/ ' .$pathDir);die;
                @mkdir($rootPath . '/' . $pathDir);
            }
            // var_dump($uploadedFile->name);die;
            $path = $pathDir . '/' . time() . rand(1000, 9999) . '.' . $uploadedFile->extension;
            // var_dump($rootPath . '/' . $path);die;
            $uploadedFile->saveAs($rootPath . '/' . $path);
            $model->$m = $path;
            $oss = new Aliyunoss();
            $oss->upload($path, $rootPath . '/' . $path);
        }
        return $this->asJson([
            'file' => $path
        ]);
        return true;
    }
}
