<?php

if ($_SERVER['HTTP_HOST'] == 'localhost:8888' || $_SERVER['HTTP_HOST'] == 'localhost') {
	defined('YII_DEBUG') or define('YII_DEBUG', true);
	defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);
    defined('YII_ENV') or define('YII_ENV', 'local');
} else {
	define('YII_ENV_DEV', false);
}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
