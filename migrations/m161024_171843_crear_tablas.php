<?php

use yii\db\Migration;

class m161024_171843_crear_tablas extends Migration
{
    public function safeUp()
    {
        // TABLAS

        $this->createTable('socio', [
            'id'                =>    $this->integer()->notNull(),
            'fechaAlta'         =>    $this->dateTime()->notNull(),
            'nombre'            =>    $this->text(),
            'apellidos'         =>    $this->text(),
            'dni'               =>    $this->string(64),
            'email'             =>    $this->string(64),
            'nombreUsuario'     =>    $this->string(64)->unique(),
            'password'          =>    $this->text(),
            'notas'             =>    $this->text(),
            'PRIMARY KEY(id)'
        ]);
        // El id del socio no se incrementa automáticamente por mysql
        $this->createIndex('idx-socio-id', 'socio', 'id');

        // ROLES

        $auth = \Yii::$app->authManager;

        $socio = $auth->createRole('socio');            
        $socio->description = 'Socio normal';     
        $auth->add($socio);


        // USUARIO POR DEFECTO

        $hoy = new \DateTime;

        $this->insert('socio', [
            'id' => 1,
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
