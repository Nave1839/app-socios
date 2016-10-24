<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use \yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
?>
<div class="login-box">
  <div class="login-logo">
    <a href="<?= Url::home(); ?>"><b>Nave</b>1839</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><?= \Yii::t('app', 'Tienes que loguearte para poder entrar') ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form'
    ]); ?>

        <div class="form-group has-feedback">
            <?= $form->field($model, 'username')->textInput(['placeholder' => \Yii::t('app', 'Nombre de usuario o Email')])->label(false); ?>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => \Yii::t('app', 'Contraseña')])->label(false); ?>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"col-xs-8\"><div class=\"checkbox icheck\"><label>{input} " . \Yii::t('app', 'Recuérdame') . "</label></div></div>",
            ]) ?>
            <div class="col-xs-4">
                <?= Html::submitButton(\Yii::t('app', 'Entrar'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
      </div>
    <?php ActiveForm::end(); ?>

    <!-- <a href="#">Olvidé mi contraseña</a><br>    -->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->