<?php 
  use \yii\helpers\Url;
  use yii\helpers\Html;
  use yii\bootstrap\ActiveForm;

  $this->title = \Yii::t('app', 'Exportar socios');
  
?>

  <div class="row">
    <div class="col-md-6">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <!-- form start -->
          <div class="box-header with-border">
            <h3 class="box-title"><?= \Yii::t('app', 'Para newsletter'); ?></h3>
          </div>
          
          <div class="box-footer">
            <a href="<?= Url::to(['socio/exportar', 'tipo' => 'newsletter']); ?>" class="btn btn-primary"><?= \Yii::t('app', 'Exportar'); ?></a>
            <a href="<?= Url::to(['socio/index']); ?>" class="btn btn-default"><?= \Yii::t('app', 'Cancelar'); ?></a>
          </div>
      </div>
      <!-- /.box -->
    </div>
  </div>