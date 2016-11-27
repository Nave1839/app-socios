<?php
	use yii\helpers\Url;

	$this->title = \Yii::t('app', 'Crear socios');

	$columnas = [
		[
			'nombre' => \Yii::t('app', 'Nombre'),
			'atributo' => 'Socio[nombre]'
		],
		[
			'nombre' => \Yii::t('app', 'Apellidos'),
			'atributo' => 'Socio[apellidos]'
		],
		[
			'nombre' => \Yii::t('app', 'DNI'),
			'atributo' => 'Socio[dni]'
		],
		[
			'nombre' => \Yii::t('app', 'Mensaje')
		]
	];
?>

<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <!-- /.box-header -->
      <div class="box-body">
		    <div class="handsontable js-handsontable" 
		    	data-columnas='<?= json_encode($columnas) ?>'
		    	data-url="<?= Url::to('/socio/actualizar') ?>"
		    	data-columna-servidor="3"
		    	data-mensaje-guardado="<?= \Yii::t('app', 'Guardado...') ?>"
		    	data-mensaje-error-servidor="<?= \Yii::t('app', 'Error en el servidor'); ?>"
		    ></div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->