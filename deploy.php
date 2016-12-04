<?php

require 'recipe/yii2-app-basic.php';

set('shared_dirs', [
	'backups'
]);

set('shared_files', ['config/db.php']);

server('prod', '188.166.174.54', 22)
    ->user('nave1839')
    ->forwardAgent()
    ->stage('production')
    ->env('branch', 'master')
    ->env('deploy_path', '/var/www/app.nave1839.org/www');

set('repository', 'git@bitbucket.org:edupoch/app.nave1839.org.git');