<div class="modal fade" id="<?= $id ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= \Yii::t('app', 'Cerrar'); ?>">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-aqua"><?= \Yii::t('app', 'Ayuda'); ?></h4>
      </div>
      <div class="modal-body">
        <?= $mensaje ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= \Yii::t('app', 'Cerrar'); ?></button>        
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>