<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$this->title = '登录';
$this->params['breadcrumbs'][] = $this->title;
$request=\Yii::$app->request;
?>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper">
      <div class="row">
        <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-full-bg">
          <div class="row w-100">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-dark text-left p-5">
                <h2>登录</h2>
                <h4 class="font-weight-light">欢迎</h4>
                <form class="pt-5" method="post" action="<?php echo Url::toRoute(['/site/login']); ?>">
                <input type="hidden" name="<?=$request->csrfParam?>" value="<?=$request->getCsrfToken()?>">
                  <div class="form-group">
                    <label for="exampleInputEmail1">用户名</label>
                    <input type="text" class="form-control"  name="account" placeholder="用户名">
                    <i class="mdi mdi-account"></i>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">密码</label>
                    <input type="password" class="form-control" name="password"  placeholder="密码">
                    <i class="mdi mdi-eye"></i>
                  </div>
                  <div class="mt-5">
                    <button class="btn btn-block btn-warning btn-lg font-weight-medium">登录</button>
                  </div>               
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- row ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>