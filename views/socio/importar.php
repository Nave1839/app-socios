<?php 
  use \yii\helpers\Url;
  use yii\helpers\Html;
  use yii\bootstrap\ActiveForm;

  $this->title = \Yii::t('app', 'Importar socios');
  
?>
<?php $form = ActiveForm::begin([
    'id' => 'importar-socio-form',
]); ?>

  <div class="row">
    <div class="col-md-6">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <!-- form start -->
          <div class="box-header with-border">
            <h3 class="box-title"><?= \Yii::t('app', 'Desde un excel'); ?></h3>
          </div>
          
          <div class="box-body">

            <?= $form->field($importacion, 'fichero')->fileInput() ?>                              

          </div>
          <!-- /.box-body -->           
          <div class="box-footer">
            <?= Html::submitButton(\Yii::t('app', 'Importar'), ['class' => 'btn btn-primary']) ?>
            <a href="<?= Url::to(['socio/index']); ?>" class="btn btn-default"><?= \Yii::t('app', 'Cancelar'); ?></a>
          </div>
      </div>
      <!-- /.box -->
    </div>
  </div>

<?php ActiveForm::end(); ?>