<?php 
  use \yii\helpers\Html;
?>
<?= 
	Html::beginForm($accion, 'post');
?>
	<?= 
	  Html::submitButton('Borrar', [
	    'class' => 'btn btn-link pull-right text-red',
	    'data-confirm' => $mensajeConfirmacion,
	  ]);
	?>
<?= 
	Html::endForm();
?>