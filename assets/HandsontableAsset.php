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
class HandsontableAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/handsontable/dist';
    public $baseUrl = '@web';

    public $css = [
        'handsontable.full.min.css'
    ];
    public $js = [        
    	'handsontable.full.min.js'
    ];    
}
