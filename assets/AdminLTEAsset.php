<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AdminLTEAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/';
    public $baseUrl = '@web';

    public $css = [
        'plugins/datatables/dataTables.bootstrap.css',
        'dist/css/AdminLTE.min.css',    
        'dist/css/skins/skin-blue.min.css',
        'plugins/iCheck/square/blue.css',
        'plugins/datepicker/datepicker3.css',
    ];
    public $js = [
        'plugins/iCheck/icheck.min.js',
        'plugins/datatables/jquery.dataTables.min.js',
        'plugins/datatables/dataTables.bootstrap.min.js',
        'plugins/datepicker/bootstrap-datepicker.js',
        'dist/js/app.min.js',        
    ];    
}
