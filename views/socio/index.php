<?php
  $this->title = \Yii::t('app', 'Socios');
?>

<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title"><?= \Yii::t('app', 'Todos los socios'); ?></h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
		    <?= $this->render('/socio/_tabla', ['socios' => $socios]); ?>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->