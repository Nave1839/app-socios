<?php 
  use \yii\helpers\Url;
  use \yii\helpers\Html;
?>
<table class="table table-bordered table-striped js-dataTable" data-url="<?= Url::to(['/socio/api']) ?>">
  <thead>
  <tr>
    <th width="60px"><?= \Yii::t('app', 'Nº Socio'); ?></th>
    <th class="js-dataTable-ordenar"><?= \Yii::t('app', 'Nombre completo'); ?></th>
    <th><?= \Yii::t('app', 'DNI'); ?></th>
    <th><?= \Yii::t('app', 'Advertencia'); ?></th>
    <th width="80px"></th>
  </tr>
  </thead>
  <tbody>
  <?php 
    if (count($socios)) :
      foreach ($socios as $socio) :        
  ?>
        <tr>
          <td><?= $socio->id ?></td>
          <td><?= $socio->nombreCompleto ?></td>
          <td><?= $socio->dni ?></td>
          <td><span class="text-yellow"><?= $socio->primerError ?></span></td>
          <td>
          <?= 
            $this->render('/site/_borrar', [
              'accion' => ['socio/borrar', 'id' => $socio->id],
              'mensajeConfirmacion' => \Yii::t('app', '¿Estás seguro de que quieres borrar este socio?')
            ]);
          ?>
            <a href="<?= Url::to(['socio/editar', 'id' => $socio->id]); ?>" class="pull-right"><?= \Yii::t('app', 'Editar'); ?></a>
          </td>
        </tr>
  <?php 
      endforeach;
    endif;
  ?>
  </tbody>
</table>