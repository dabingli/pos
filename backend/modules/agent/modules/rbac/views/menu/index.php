<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\widgets\StoreMenu;
use yii\base\Widget;
$this->title = '菜单列表';
$this->params['breadcrumbs'][] = '代理商信息';
$this->params['breadcrumbs'][] = '代理商平台管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">

			<div class="x_content">

				<!-- start accordion -->
				<div class="accordion" id="accordion1" role="tablist"
					aria-multiselectable="true">
					<div class="panel"></div>
					<div class="col-sm-8">
						<h2>菜单列表</h2>
						<div style="min-height: 380px; padding: 30px 25px;">
							<div class="form-group">
								<button class="btn btn-primary" id="expandTree_btn_lessonManage">展开全部</button>
								<button class="btn btn-primary"
									id="collapseTree_btn_lessonManage">折叠全部</button>
								<a class="btn btn-success" href="<?= Url::toRoute(['edit']); ?>">新
									增</a>&nbsp;&nbsp;&nbsp;
								<div class="col-sm-6" style="padding-left: 0;">
									<input id="search-departmentsTree-lessonManage"
										class="col-sm-5 form-control" type="text"
										placeholder="输入关键字, 匹配菜单会高亮显示" />
								</div>
							</div>
							<div id="treeview-checkable" class="treeview">
								<ul class="list-group">

								</ul>
							</div>
						</div>
					</div>

				</div>
				<!-- end of accordion -->

			</div>
		</div>
	</div>
</div>
<?php echo StoreMenu::widget(['search'=>'search-departmentsTree-lessonManage','id'=>'treeview-checkable','expandTree'=>'expandTree_btn_lessonManage','collapseTree'=>'collapseTree_btn_lessonManage']); ?>
