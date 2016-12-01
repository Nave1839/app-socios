<?php 
	use \yii\helpers\Url;
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;

	$this->title = \Yii::t('app', 'Errores en los socios');
	
?>

<div class="row">
	<div class="col-md-12">
		<!-- Horizontal Form -->
		<div class="box box-info">        
			<div class="box-body">
				<table class="table table-bordered table-striped js-dataTable">
					<thead>
						<tr>
							<th class="js-dataTable-ordenar" width="20px">#</th>
							<th width="50px"><?= \Yii::t('app', 'Campo'); ?></th>
							<th><?= \Yii::t('app', 'Error'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
							if ($errores && count($errores)) :
								$e = 1;
								foreach ($errores as $error) :
							?>
									<tr>
										<td>
											<?= $e; ?>
										</td>
										<td>
											<?= $error['campo'] ?>
										</td>
										<td>
											<?= $error['mensaje']; ?>
										</td>
									</tr>            
							<?php 
									$e++;
								endforeach;
							endif;
						?>				  
					</tbody>
				</table>
			</div>          
		</div>
		<!-- /.box -->
	</div>
</div>