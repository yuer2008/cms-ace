<?php
use yii\widgets\LinkPager;
?>
<div class="row">
	<div class="col-xs-12">
		<div class="table-responsive">
			<table id="sample-table-1" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="center">
							<label>
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</th>
						<th>用户名</th>
						<th>Email</th>
						<th class="hidden-480">手机</th>

						<th>
							<i class="icon-time bigger-110 hidden-480"></i>
							Update
						</th>
						<th class="hidden-480">Status</th>

						<th></th>
					</tr>
				</thead>

				<tbody>
					<? foreach($mber as $k=>$v):?>
					<tr>
						<td class="center">
							<label>
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</td>

						<td>
							<a href="#"><?=$v->username?></a>
						</td>
						<td>$45</td>
						<td class="hidden-480"><?=$v->email?></td>
						<td><?=$v->mobile?></td>

						<td class="hidden-480">
							<? if($v->status == 1):?>
							<span class="label label-sm label-warning">Expiring</span>
							<?elseif($v->status == 2):?>
							<span class="label label-sm label-success">Registered</span>
							<?elseif($v->status == 3):?>
							<span class="label label-sm label-inverse arrowed-in">Flagged</span>
							<?endif;?>
						</td>

						<td>
							<div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
								<button class="btn btn-xs btn-success">
									<i class="icon-ok bigger-120"></i>
								</button>

								<button class="btn btn-xs btn-info">
									<i class="icon-edit bigger-120"></i>
								</button>

								<button class="btn btn-xs btn-danger">
									<i class="icon-trash bigger-120"></i>
								</button>

								<button class="btn btn-xs btn-warning">
									<i class="icon-flag bigger-120"></i>
								</button>
							</div>

							<div class="visible-xs visible-sm hidden-md hidden-lg">
								<div class="inline position-relative">
									<button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown">
										<i class="icon-cog icon-only bigger-110"></i>
									</button>

									<ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">
										<li>
											<a href="#" class="tooltip-info" data-rel="tooltip" title="View">
												<span class="blue">
													<i class="icon-zoom-in bigger-120"></i>
												</span>
											</a>
										</li>

										<li>
											<a href="#" class="tooltip-success" data-rel="tooltip" title="Edit">
												<span class="green">
													<i class="icon-edit bigger-120"></i>
												</span>
											</a>
										</li>

										<li>
											<a href="#" class="tooltip-error" data-rel="tooltip" title="Delete">
												<span class="red">
													<i class="icon-trash bigger-120"></i>
												</span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</td>
					</tr>
					<?endforeach;?>
				</tbody>
			</table>
		</div><!-- /.table-responsive -->
	</div><!-- /span -->
</div><!-- /row -->
<?=LinkPager::widget(['pagination' => $pages]); ?>