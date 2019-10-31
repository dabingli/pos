<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
//use common\services\User;
use common\models\user\User;
?>
<div class="modal-dialog"  style="width:65%">


		<div class="modal-content">


			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle">冻结用户</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['frozen-do']); ?>" method="post"
					novalidate="novalidate">

<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					<table class="table table-striped table-bordered dataTable no-footer">
						<tr>
							<th>代理商编号</th>
							<th>代理商</th>
							<th>冻结收益类型</th>
						</tr>
						<?php foreach($model as $m){ ?>
						<tr>
							<td><?php echo $m->user_code;?></td>
							<td><?php echo $m->user_name;?></td>
							<td>
							<?php if(Yii::$app->params['agentAppUser']->id!=$m->id){ ?>
								<label>
									<input style="padding: 7px 10px 0px 0px;" <?php if($m->frozen_earnings==User::FROZEN_EARNINGS){ ?> checked="true" <?php } ?> name="data[<?php echo $m->id ?>][frozen_earnings]" type="checkbox" value="2" /> 冻结返现收益
								</label>&nbsp;&nbsp;
								<label>
									<input style="padding: 7px 10px 0px 0px;" <?php if($m->frozen_distributing==User::FROZEN_DISTRIBUTING){ ?> checked="true" <?php } ?> name="data[<?php echo $m->id ?>][frozen_distributing]" type="checkbox" value="2" /> 冻结分润收益
								</label>
								<?php } ?>
							</td>
						</tr>
						<input type="hidden" name="ids[]" value="<?= $m->id; ?>">
						<?php } ?>
					</table>

					<div class="form-group">
						<label class="col-md-1 text-right control-label"></label>
						<div class="col-md-4" style="padding: 0px 10px 0px 0px;">
						</div>
						<div class="col-md-4">
							<button id="submit-btn" type="submit" class="btn btn-primary">提&nbsp;&nbsp;交</button>
							
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>