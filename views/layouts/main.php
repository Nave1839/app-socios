<?php

/* @var $this \yii\web\View */
/* @var $content string */

use \yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\AppAsset;

AppAsset::register($this);

$controlador = $this->context->id;
$accion = $this->context->action->id;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue layout-boxed sidebar-mini">
<?php $this->beginBody() ?>

<?php 
    $error = \Yii::$app->session->getFlash('error');
    if ($error) :
?>
      <div class="mensaje-flash js-mensaje-flash alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> <?= \Yii::t('app', 'Error'); ?></h4>
          <?= $error; ?>
      </div>
<?php
    endif;

    $ok = \Yii::$app->session->getFlash('ok');
    if ($ok) :
?>
      <div class="mensaje-flash js-mensaje-flash alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-check"></i> <?= \Yii::t('app', 'OK'); ?></h4>
        <?= $ok; ?>
      </div>
<?php
    endif;
?>    


<div class="wrapper">
    <header class="main-header">
        <a href="<?= Url::home() ?>" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>Nave</b></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>Nave</b>1839</span>
        </a>

        <nav class="navbar navbar-static-top" role="navigation"> 
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
              <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
              <?php
                  echo Nav::widget([
                      'options' => ['class' => 'nav navbar-nav'],
                      'items' => [
                          [
                              'label' => \Yii::t('app', 'Salir'),
                              'url' => ['/site/logout'],
                              'linkOptions' => ['data-method' => 'post']
                          ],
                      ],
                  ]);
              ?>
            </div>
        </nav>

    </header>

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
          <li class="treeview <?= $controlador == 'socio' ? 'active' : '' ?>">
            <a href="#">
              <i class="fa fa-users"></i> <span> <?= \Yii::t('app', 'Socios'); ?></span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <li class="<?= $controlador == 'socio' && $accion == 'index' ? 'active' : '' ?>" ><a href="<?= Url::to(['socio/index']) ?>"><i class="fa fa-circle-o"></i> <?= \Yii::t('app', 'Todos'); ?></a></li>
              <li class="<?= $controlador == 'socio' && $accion == 'nuevo' ? 'active' : '' ?>" ><a href="<?= Url::to(['socio/nuevo']) ?>"><i class="fa fa-plus"></i> <?= \Yii::t('app', 'Crear 1'); ?></a></li>
              <li class="<?= $controlador == 'socio' && $accion == 'multiple' ? 'active' : '' ?>" ><a href="<?= Url::to(['socio/multiple']) ?>"><i class="fa fa-table"></i> <?= \Yii::t('app', 'Crear más de 1'); ?></a></li>
              <li class="<?= $controlador == 'socio' && $accion == 'importar' ? 'active' : '' ?>" ><a href="<?= Url::to(['socio/importar']) ?>"><i class="fa fa-upload"></i> <?= \Yii::t('app', 'Importar'); ?></a></li>
              <li class="<?= $controlador == 'socio' && $accion == 'errores' ? 'active' : '' ?>" ><a href="<?= Url::to(['socio/errores']) ?>"><i class="fa fa-exclamation-triangle"></i> <?= \Yii::t('app', 'Errores'); ?></a></li>
            </ul>
          </li>        
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>

    <div class="content-wrapper">
      <section class="content-header">
        <h1>
          <?= $this->title ?>
        </h1>        
      </section>
      <section class="content">
        <?= $content ?>
      </section>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left"><i class="fa fa-creative-commons"></i> Nave 1839 <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
