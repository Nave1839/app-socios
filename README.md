Hemos desarrollado esta aplicación para que nos permita guardar, consultar y modificar los datos de nuestros socios de una forma sencilla.

Está basada en [Yii 2](http://www.yiiframework.com/), un framework de desarrollo de aplicaciones web en PHP.

Requisitos técnicos
-------------------

- PHP 5.4.0 ó superior
- MySQL
- Apache
- Composer

Instalación
-----------

Si no tienes el [Composer](http://getcomposer.org/), puedes instalarlo siguiendo las instrucciones en [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

```bash
git clone https://github.com/Nave1839/app-socios
cd app-socios
composer global require "fxp/composer-asset-plugin:^1.2.0"
composer install
```

Configuración
-------------

### Base de datos

Edita el fichero `config/db.php` con los datos de conexión a tu base de datos, por ejemplo:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

Una vez hecho esto, puedes crear la estructura de tablas ejecutando el siguiente código desde un terminal

```bash
yii migrate
```

Ejecución
---------

Configura el Apache para que la carpeta raíz sea `app-socios/web`. Con eso ya deberías poder visualizar la aplicación en:

~~~
http://localhost:8888/
~~~
