<?php
	use yii\helpers\Url;
	use app\models\Socio;

	$this->title = \Yii::t('app', 'Crear socios');

	$columnas = [
		[
			'nombre' => \Yii::t('app', 'Nº Socio'),
			'atributo' => 'Socio[id]',
			'ancho' => 50,
			'tipo' => 'id',
			'inicial' => Socio::siguienteId()
		],
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
			'atributo' => 'Socio[dni]',
			'ancho' => 100
		],
		[
			'nombre' => \Yii::t('app', 'Email'),
			'atributo' => 'Socio[email]'
		],
		[
			'nombre' => \Yii::t('app', 'Mensaje'),
			'tipo' => 'servidor'
		]
	];
?>

<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <!-- /.box-header -->
      <div class="box-body">
		    <div class="handsontable js-handsontable" 
		    	data-columnas='<?= json_encode($columnas) ?>'
		    	data-url="<?= Url::to('/socio/actualizar') ?>"
		    	data-mensaje-guardando="<?= \Yii::t('app', 'Guardando...') ?>"
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