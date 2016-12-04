<?php
namespace app\commands;

use yii\console\Controller;

class BackupController extends Controller
{
    public function actionIndex()
    {
    	$db = \Yii::$app->db;

    	if ($db) {
    		if ($db->dsn && $db->username && $db->password) {
    			if (preg_match('/mysql:host=([^;]+);dbname=([^;]+)/', $db->dsn, $partes)) {
    				$directorio = \Yii::getAlias('@app/backups');
    				
    				$comando = 'mysqldump --user=' . $db->username . ' --password=' . $db->password . ' ' . $partes[2] . ' > ' . $directorio . '/nave1839-$(date +%Y-%m-%d_%H-%M-%S).sql';
    				
    				shell_exec($comando);	
    				return Controller::EXIT_CODE_NORMAL;
    			}
	    		
	    	}
        }

        return Controller::EXIT_CODE_ERROR;
    }
}
