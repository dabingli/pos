<?php 
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="container-scroller">
	<div class="container-fluid page-body-wrapper">
		<div class="row">
			<div
				class="content-wrapper full-page-wrapper d-flex align-items-center text-center error-page bg-info">
				<div class="col-lg-7 mx-auto text-white">
					<div class="row align-items-center d-flex flex-row">
						<div class="col-lg-6 text-lg-right pr-lg-4">
							<h1 class="display-1 mb-0">403</h1>
						</div>
						<div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
							<h2>对不起!</h2>
							<h3 class="font-weight-light">禁止访问!</h3>
						</div>
					</div>
					<div class="row mt-5">
						<div class="col-12 text-center mt-xl-2">
							<a class="text-white font-weight-medium" href="<?php echo Url::toRoute(['/']); ?>">回到首页</a>
						</div>
					</div>
					<div class="row mt-5">
						<div class="col-12 mt-xl-2">
							<p class="text-white font-weight-medium text-center">Copyright
								&copy; 2018 All rights reserved.</p>
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