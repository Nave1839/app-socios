<?php 
	use \yii\helpers\Url;
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;

	$esNuevo = $socio->id == null;

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

		      	<?php 
		      		if (!$esNuevo) {
		      			echo $form->field($socio, 'id')->textInput();

		      			$socio->fechaAlta = \Yii::$app->formatter->asDate($socio->fechaAlta);
		      			echo $form->field($socio, 'fechaAlta')->staticControl();
		      		}
		      	?>

		      	<?= $form->field($socio, 'nombre')->textInput(); ?>	      	    

		      	<?= $form->field($socio, 'apellidos')->textInput(); ?>	      	    

		      	<?= $form->field($socio, 'dni')->textInput(); ?>

  		      	<?php 
  		      		if (!$socio->esCorrectaLetraDni()) :
  		      			$letra = $socio->letraDniCorrecta;
  		      	?>	      	
  		      		<div class="form-group has-warning">
  	                	<span class="help-block"><?= \Yii::t('app', 'La letra del DNI debería ser {letra}', ['letra' => $letra]); ?></span>
  	                </div>    		      	
  		      	<?php 
  		      		endif;
  		      	?>

		      	<?= $form->field($socio, 'email')->textInput(); ?>

		      	<?= $form->field($socio, 'notas')->textArea(['rows' => 12]); ?>

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
		  <!-- Horizontal Form -->
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
		</div>
	</div>

<?php ActiveForm::end(); ?>