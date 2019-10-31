<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\services\StoreMenuServices;
$this->title = '菜单添加';
$this->params['breadcrumbs'][] = '代理商信息';
$this->params['breadcrumbs'][] = '代理商平台管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$menuServices=new StoreMenuServices();
?>
<div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">添加菜单</font></font><small><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"></font></font></small></h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <form method="post" action="<?php echo Url::toRoute(['/agent/rbac/menu/edit','id'=>\Yii::$app->request->get('id')]); ?>" class="form-horizontal form-label-left">
					
					<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">上级菜单<span  class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                        	
                          <select  class="form-control" name="parent_id" required="required">
                          <?php $getSelectData=$menuServices->getSelectData(); array_unshift($getSelectData,['id'=>'0','name'=>'顶级菜单']); ?>
    
                          	<?php foreach($getSelectData as $v){ ?>
                          	<?php if($v['id']==$model->parent_id){ ?>
                          	<option selected value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                          	<?php }else{ ?>
                          	<option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                          	<?php } ?>
                          	<?php } ?>
                          </select>
                          <font color="red"></font>
                        </div>
                      </div>

                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" >菜单名称<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input value="<?php echo $model->name;?>" type="text"  name="name" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" >图标
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input value="<?php echo $model->icon;?>" type="text"  name="icon" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="order">排序
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input value="<?php echo $model->order;?>" type="number" id="order" name="order" data-validate-minmax="1,256" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="website">URL 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input value="<?php echo $model->route;?>" type="text" id="website" name="route"  placeholder="请输入链接地址" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>


                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="textarea">备注 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea name="remarks" class="form-control col-md-7 col-xs-12"><?php echo $model->remarks;?></textarea>
                        </div>
                      </div>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                          <button type="submit" class="btn btn-success">提交</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>