<?php
	use yii\helpers\Url;
	use app\models\Socio;

	$this->title = \Yii::t('app', 'Crear socios');

	$columnas = [
		[
			'nombre' => \Yii::t('app', 'Nº Socio'),
			'atributo' => 'Socio[id]',
			'ancho' => 70,
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
			'nombre' => \Yii::t('app', '¿Newsletter?'),
			'atributo' => 'Socio[quiereNewsletter]',
			'ancho' => 80,
			'tipo' => 'checkbox'
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
      <div class="box-header">
      	<br>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-toggle="modal" data-target="#ayuda-socio-multiple">
            <i data-toggle="tooltip" title="" data-original-title="<?= \Yii::t('app', 'Ayuda'); ?>" class="fa fa-question fa-lg text-aqua"></i>
          </button>
        </div>
      </div>
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

<?= 
  $this->render('/site/_ayuda', [
    'id' => 'ayuda-socio-multiple', 
    'mensaje' => 
      \Yii::t('app', 
        '<p>Esta página te permite <b>crear más de un socio</b> a la vez. Para ello, sigue estos pasos:</p><ol><li>Haz click en la casilla bajo la columna "Nombre" de la primera fila y comienza a escribir.</li><li>Para pasar a la siguiente columna, puedes pulsar la tecla <b>tabulador</b>.</li><li>En la columna "Newsletter?" marca si el socio quiere recibir nuestra newsletter mensual. Puedes usar la <b>barra espaciadora</b> si te resulta más cómodo.</li><li>Cuando pases a la siguiente fila, el sistema te indicará cómo ha ido la operación en la columna "Mensaje": <ul><li><span class="text-green">Socio X creado</span><br>El socio se ha guardado <b>correctamente</b>.</li><li><span class="text-yellow">Socio X creado pero la letra del DNI debería ser la Y</span><br>El socio se ha guardado pero <b>puede haber algún problema</b> con el DNI.<br>La letra del DNI es un código de verificación que se calcula aplicando una fórmula a los números anteriores. Por ejemplo, si los números del DNI son 12345678, la letra siempre será la Z.<br>La aplicación utiliza esta fórmula para calcular la letra con los números que has introducido y comprueba que sea la misma que la que has escrito. Si no coinciden, el sistema nos da esta advertencia. Pero esto puede haber ocurrido porque (a) la letra sea incorrecta, o (b) porque algún número sea incorrecto.<br>Es decir, el sistema solo sabe que algo va mal y nos da una pequeña pista con la letra, así que lo mejor es que <b>revises que lo que has escrito coincide con lo que pone la hoja</b>. <br>De todas formas, <b>esto es solo es una advertencia</b> porque el socio se guarda igualmente. Puede que realmente no haya ningún error porque el socio sea extranjero. Esta fórmula solo tiene sentido aplicarla en los DNIs españoles. De ser así, podemos <b>obviar la advertencia</b> y pasar a la siguiente fila.</li><li><span class="text-red">El Nombre/Apellido/DNI no puede quedar en blanco</span><br><b>Es necesario cubrir estos tres campos</b> para que alguien se haga socio de la Nave. Si el socio no ha cubierto uno de estos campos o no entiendes lo que ha escrito, puedes <b>tacharlo en la hoja</b> y pasar al siguiente.</li><li><span class="text-red">El DNI ya ha sido utilizado</span><br>El socio ya forma parte de nuestra base de datos. Puedes <b>tacharlo en la hoja</b> y pasar al siguiente.</li><li><span class="text-red">Email no es una dirección de correo válida</span><br>Aunque el correo electrónico es opcional, sí que tiene que tener un <b>formato válido</b> del tipo cuenta@ejemplo.com.<br>Si no entiendes lo que pone en la hoja, puedes dejar este campo sin cubrir.</li></ul></li><li>Una vez guardado el socio, <b>apunta el "Nº Socio" en la hoja</b>.</li><li>Vuelve al punto 1 con la siguiente fila.</li></ol>'
      )
  ]);
?>