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
		    <?php 
		    	if (!$esNuevo) :
		    		$fecha = \Yii::$app->formatter->asDate($socio->fechaAlta);		    		
		    ?>
		    	<div class="box-header with-border">
		    		<h3 class="box-title"><?= sprintf(\Yii::t('app', 'Socio #%s desde el %s'), $socio->id, $fecha); ?></h3>
		    	</div>
		    <?php 
		    	endif;
		    ?>
		      <div class="box-body">

		      	<?php 
		      		if (!$esNuevo) {
		      			echo $form->field($socio, 'id')->textInput();
		      		}
		      	?>

		      	<?= $form->field($socio, 'nombre')->textInput(); ?>	      	    

		      	<?= $form->field($socio, 'apellidos')->textInput(); ?>	      	    

		      	<?= $form->field($socio, 'dni')->textInput(); ?>

		      	<?= $form->field($socio, 'nombreUsuario')->textInput(); ?>	      	    

		      	<?= $form->field($socio, 'password')->passwordInput()->label(\Yii::t('app', 'Nueva contraseña')); ?>	      	     	      	    		      	

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
			<div class="box-body">

				<?= $form->field($socio, 'notas')->textArea(['rows' => 12]); ?>	      	          	    

			</div>		       
		  </div>
		  <!-- /.box -->
		</div>
	</div>

<?php ActiveForm::end(); ?>