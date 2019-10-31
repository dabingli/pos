<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="modal-dialog" style="width:85%">


		<div class="modal-content">


			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle">查看下级代理商</span>&nbsp;
				</h4>
			</div>

			<div class="modal-body form-horizontal">
					<div id="toolbar" class="btn-group">
                        <button onclick="download()"  type="button" class="btn btn-success add">
                            <span class="glyphicon glyphicon-import" aria-hidden="true"></span> 导出
                        </button>
                        <input type="hidden" name="id" value="<?php echo $model->id; ?>"/>
               	 	</div>
						<table class="table">
           					<thead>
               					 <tr>
                    				<th>代理商</th>
                    				<th>代理商编号</th>
                    				<th>手机号</th>
                                     <th>是否实名</th>
                    				<th>结算价<h6>[机具类型/贷记卡结算价/借记卡结算价(封顶)/返现单价]</h6></th>
                    				<th>状态</th>
               					</tr>
           					</thead>
            				<thead>
            				
                				<tr>
                					<td ajax="0" lv='1' class="tree replace_nbsp" data-id="<?php echo $model->id; ?>" style="cursor:pointer">
                                        <i class="glyphicon glyphicon-triangle-right"></i>&nbsp;&nbsp;
                                        <i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;
                                        <?php echo !empty($model->real_name) ? $model->real_name : $model->user_name; ?></td>
                    				<td><?php echo $model->user_code; ?></td>
                    				<td><?php echo $model->mobile; ?></td>
                    				<td><?php echo $model->getAuthentication(); ?></td>
                    				<td><?php
                                        foreach($model->userSettlementMany as $val){
                                            echo $val->agentProductType->productType->name . '/' . $val->level_cc_settlement . '/' . $val->level_dc_settlement
                                                . '('. $val->capping .')'  . '/' . $val->cash_money .'</br>';
                                        }
                                        ?>
                                    </td>
                    				<td><?php echo $model->getStatus(); ?></td>
                    			</tr>
                				
            				</thead>
            			</table>
						
					</div>
			
			</div>
		</div>
	</div>

<script>
    function download(){
        var id = $('input[name="id"]').val();
        window.location.href = '<?php echo Url::toRoute(['download']); ?>?id='+ id;
    }
</script>