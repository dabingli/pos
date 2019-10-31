<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\assets\AppAsset;

AppAsset::register($this);
?>
<link href="<?php echo Yii::$app->request->baseUrl ?>/css/zx.css" rel="stylesheet"></head>

<div class="modal-dialog" style="width: 60%">


		<div class="modal-content" id="detail_body">


			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
					<span class="text-danger"><b><span id="detail_modal_close"
							class="glyphicon glyphicon-remove-sign"
							style="font-size: 18px; cursor: pointer;"></span></b></span>
				</button>
				<h4 class="modal-title text-danger" id="ajax-view-label">
					<span id="modalDetailTitle">分配菜单</span>&nbsp;
				</h4>
			</div>
			
			<div class="modal-body form-horizontal">
			
				<form id="form" class="form-horizontal"
					action="<?php echo Url::toRoute(['menu-add']); ?>" method="post"
					novalidate="novalidate">

					<input type="hidden" name="id" value="<?php echo Yii::$app->request->post('id') ?>">
					<?php foreach($model->getMenus() as $val){ ?>
					<input class="has" type="hidden" name="menu_ids[]" value="<?php echo $val; ?>">
					<?php } ?>
					<input type="hidden" name="<?= \Yii::$app->request->csrfParam?>" value="<?=\Yii::$app->request->getCsrfToken()?>">
					<div class="form-group">
								<a class="btn btn-primary" id="expandTree_btn_lessonManage">展开全部</a>
								<a class="btn btn-primary"
									id="collapseTree_btn_lessonManage">折叠全部</a>
								
								<div class="col-sm-6" style="padding-left: 0;">
									<input id="search-departmentsTree-lessonManage"
										class="col-sm-5 form-control" type="text"
										placeholder="输入关键字, 匹配成功会高亮显示" />
								</div>
							</div>
							<div class="form-group">
							<div id="treeview-checkable" class="treeview">
					<ul class="list-group">

					</ul>
			</div>
							</div>
					<div class="form-group">
						<label class="col-md-3 text-right control-label"></label>
						<div class="col-md-8" style="padding: 0px 10px 0px 0px;">
							<button  type="submit" class="btn btn-primary" id="submit-btn">提&nbsp;&nbsp;交</button>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
	<script src="<?php echo Yii::$app->request->baseUrl ?>/js/bootstrap-treeview.js"></script>
	<script type="text/javascript">
		 var defaultData =<?php echo json_encode($data); ?>;

			    var $checkableTree = $('#treeview-checkable').treeview({
			      data: defaultData,
			      showIcon: true,
			      showCheckbox: true,
			      selectedBackColor: '#ffffcc',
			      color: "#428bca",
			      selectedColor: '#000000',
			      showTags: false,
			      //nodeIcon: "glyphicon glyphicon-plus",
			      onNodeChecked: function (event, node) {
			    	  console.log(node);
			    	  if(node['id']>0){
				    	  if($("#form .has"+node['id']).val()>0){
				    		  $("#form .has"+node['id']).remove();
				    	  }
			    		  $('#form').append('<input class="has'+node['id']+'" type="hidden" name="menu_ids[]" value="'+node['id']+'">');
			    	  }
			    	  //<input type="hidden" name="perms[]" value="">
			        //$('#checkable-output').prepend('<p>' + node.text + ' was checked</p>');
			        // console.log(event);
			        // console.log(node.nodes);

			        // $.each(node.nodes, function (index, value, array) {
			        //   console.log(value);

			        // });
			        //checkAllParent(node);
			        checkAllSon(node);
			        //alert(node.nodeId);
			      },
			      
			      onNodeUnchecked: function (event, node) {
			        //$('#checkable-output').prepend('<p>' + node.text + ' was unchecked</p>');
			    	  console.log(node);
			    	  if(node['id']>0){
			    		  $("#form .has"+node['id']).remove();
			    	  }
			        uncheckAllParent(node);
			        uncheckAllSon(node);
			        //alert(node.nodeId);
			      }

			    });
			    /* $('#treeview-checkable').on('nodeSelected', function (event, data) {
			      // Your logic goes here 
			      //   console.log('222222222222222222');
			      console.log(data.id);
			    }); */



			    var nodeCheckedSilent = false;
			    function nodeChecked(event, node) {
			      if (nodeCheckedSilent) {
			        return;
			      }
			      nodeCheckedSilent = true;
			      checkAllParent(node);
			      checkAllSon(node);
			      nodeCheckedSilent = false;
			    }

			    var nodeUncheckedSilent = false;
			    function nodeUnchecked(event, node) {
			      if (nodeUncheckedSilent)
			        return;
			      nodeUncheckedSilent = true;
			      uncheckAllParent(node);
			      uncheckAllSon(node);
			      nodeUncheckedSilent = false;
			    }

			    //选中全部父节点  
			    function checkAllParent(node) {
			      $('#treeview-checkable').treeview('checkNode', node.nodeId, { silent: true });
			      var parentNode = $('#treeview-checkable').treeview('getParent', node.nodeId);
			      if (!("nodeId" in parentNode)) {
			        return;
			      } else {
			        checkAllParent(parentNode);
			      }
			    }
			    //取消全部父节点  
			    function uncheckAllParent(node) {
			      $('#treeview-checkable').treeview('uncheckNode', node.nodeId, { silent: true });
			      var siblings = $('#treeview-checkable').treeview('getSiblings', node.nodeId);
			      var parentNode = $('#treeview-checkable').treeview('getParent', node.nodeId);
			      if (!("nodeId" in parentNode)) {
			        return;
			      }
			      var isAllUnchecked = true;  //是否全部没选中  
			      for (var i in siblings) {
			        if (siblings[i].state.checked) {
			          isAllUnchecked = false;
			          break;
			        }
			      }
			      if (isAllUnchecked) {
			        uncheckAllParent(parentNode);
			      }

			    }

			    //级联选中所有子节点  
			    function checkAllSon(node) {
			      $('#treeview-checkable').treeview('checkNode', node.nodeId, { silent: true });
			      if (node.nodes != null && node.nodes.length > 0) {
			        for (var i in node.nodes) {
			          checkAllSon(node.nodes[i]);
			        }
			      }
			    }
			    //级联取消所有子节点  
			    function uncheckAllSon(node) {
			      $('#treeview-checkable').treeview('uncheckNode', node.nodeId, { silent: true });
			      if (node.nodes != null && node.nodes.length > 0) {
			        for (var i in node.nodes) {
			          uncheckAllSon(node.nodes[i]);
			        }
			      }
			    } 
			    $('#expandTree_btn_lessonManage').click(function() {   // 展开所有节点
			    	$('#treeview-checkable').treeview('expandAll');
		    	});
		    	$('#collapseTree_btn_lessonManage').click(function() { //合并所有节点
		    		$('#treeview-checkable').treeview('collapseAll');
		    	}); 	
		    	$('#search-departmentsTree-lessonManage').on('input', function (e) { // 搜索指定节点, 并高亮显示
		        	$('#treeview-checkable').treeview('search', [ $(this).val(), { ignoreCase: false, exactMatch: false }]);
		        });
		    	$('#treeview-checkable').treeview('expandAll');       

</script>   