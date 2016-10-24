<?php

use yii\db\Migration;

class m161024_171843_crear_tablas extends Migration
{
    public function safeUp()
    {
        // TABLAS

        $this->createTable('socio', [
            'id'                =>    $this->primaryKey(),
            'fechaAlta'         =>    $this->dateTime()->notNull(),
            'nombre'            =>    $this->text()->notNull(),
            'apellidos'         =>    $this->text()->notNull(),
            'dni'               =>    $this->string(64)->unique(),
            'email'             =>    $this->string(64)->unique(),
            'nombreUsuario'     =>    $this->string(64)->unique(),
            'password'          =>    $this->text(),
            'notas'             =>    $this->text(),
        ]);

        // ROLES

        $auth = \Yii::$app->authManager;

        $socio = $auth->createRole('socio');            
        $socio->description = 'Socio normal';     
        $auth->add($socio);


        // USUARIO POR DEFECTO

        $hoy = new \DateTime;

        $this->insert('socio', [
            'nombre' => 'Administrador',
            'apellidos' => 'Por defecto',
            'dni' => 'noexiste',
            'nombreUsuario' => 'admin',
            'password' => 'sha256:1000:OX91QOfWn2e0c23oq+rfbDqfwTgpR0DA:ABRHODKh+ItWXD2r/vcSH3AzV7W/WUnv',
            'fechaAlta' => $hoy->format('Y-m-d H:i:s')
        ]);
        $auth->assign($socio, 1);

    }

    public function down()
    {
        echo "m160315_070038_crear_tablas cannot be reverted.\n";

        return false;
    }
}
