<?php 
	use \yii\helpers\Url;
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\Socio;

	$esNuevo = $socio->fechaAlta == null;

	if ($esNuevo) {
		$this->title = \Yii::t('app', 'Nuevo socio');
	} else {
		$this->title = $socio->nombreCompleto;
	}
	
?>
<?php $form = ActiveForm::begin([
	'id' => 'socio-form',
]); ?>

	<div class="row">
		<div class="col-md-6">
		  <!-- Horizontal Form -->
		  <div class="box box-info">
			<!-- form start -->
			<div class="box-header with-border">
				<h3 class="box-title"><?= \Yii::t('app', 'Datos del socio'); ?></h3>
			</div>
			  <div class="box-body">

				<?= $form->field($socio, 'id')->textInput(); ?>

				<?php 
					if (!$esNuevo) {
						$socio->fechaAlta = \Yii::$app->formatter->asDate($socio->fechaAlta);
						echo $form->field($socio, 'fechaAlta')->staticControl();
					}
				?>

				<?= $form->field($socio, 'nombre')->textInput(); ?>	      	    

				<?= $form->field($socio, 'apellidos')->textInput(); ?>	      	    

				<?= $form->field($socio, 'dni')->textInput(); ?>

				<?php 
					$erroresEnDNI = $socio->erroresEnDNI();
					if (count($erroresEnDNI)) :
						$error = $erroresEnDNI[0];

						if ($error == Socio::ERROR_LETRA_DNI_INCORRECTA) {	
							$letra = $socio->letraDniCorrecta;
							$mensaje = \Yii::t('app', 'La letra del DNI debería ser {letra}', ['letra' => $letra]);
						} else {
							$mensaje = $socio->mensajeDeError($error);
						}
					?>
					<div class="form-group has-warning">
						<span class="help-block"><?= $mensaje ?></span>
					</div>    		      	
				<?php 
					endif;
				?>

				<?= $form->field($socio, 'email')->textInput(); ?>		      	

			  </div>
			  <!-- /.box-body -->		        
			  <div class="box-footer">
					<?= Html::submitButton(\Yii::t('app', 'Guardar'), ['class' => 'btn btn-primary']) ?>
					<a href="<?= Url::to(['socio/index']); ?>" class="btn btn-default"><?= \Yii::t('app', 'Cancelar'); ?></a>	      	
			  </div>
		  </div>
		  <!-- /.box -->
		</div>

		<div class="col-md-6">

		<?php 
			if ($socio->id == 1) :
		?>
				<div class="box box-info">
					<!-- form start -->
					<div class="box-header with-border">
						<h3 class="box-title"><?= \Yii::t('app', 'Datos de acceso a la aplicación'); ?></h3>
					</div>
					<div class="box-body">

						<?= $form->field($socio, 'nombreUsuario')->textInput(); ?>	      	    

						<?= $form->field($socio, 'password')->passwordInput()->label(\Yii::t('app', 'Nueva contraseña')); ?>

					</div>		       
				</div>
				<!-- /.box -->
		<?php 
			endif;
		?>

			<div class="box box-info">
				<!-- form start -->
				<div class="box-header with-border">
					<h3 class="box-title"><?= \Yii::t('app', 'Notas'); ?></h3>
				</div>
				<div class="box-body">

					<?= $form->field($socio, 'notas')->textArea(['rows' => 12])->label(false); ?>

				</div>		       
			</div>
			<!-- /.box -->
		</div>
	</div>

<?php ActiveForm::end(); ?>