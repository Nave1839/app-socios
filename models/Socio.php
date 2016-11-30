<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "socio".
 *
 * @property integer $id
 * @property string $fechaAlta
 * @property string $nombre
 * @property string $apellidos
 * @property string $dni
 * @property string $email
 * @property string $nombreUsuario
 * @property string $password
 * @property string $notas
 *
 */
class Socio extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'socio';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechaAlta', 'nombre', 'apellidos', 'dni'], 'required'],
            [['fechaAlta'], 'safe'],
            [['nombre', 'apellidos', 'password', 'notas'], 'string'],
            [['dni', 'email', 'nombreUsuario'], 'string', 'max' => 64],
            [['nombreUsuario'], 'match', 'pattern' => '/^[a-z]\w*$/i', 'message' => 'El nombre de usuario no tiene un formato válido'],
            [['dni'], 'unique'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['nombreUsuario', 'id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'Nº de Socio'),
            'fechaAlta' => \Yii::t('app', 'Fecha de alta'),
            'nombre' => \Yii::t('app', 'Nombre'),
            'apellidos' => \Yii::t('app', 'Apellidos'),
            'dni' => \Yii::t('app', 'DNI'),
            'email' => \Yii::t('app', 'Email'),
            'nombreUsuario' => \Yii::t('app', 'Nombre de usuario'),
            'password' => \Yii::t('app', 'Contraseña'),
            'notas' => \Yii::t('app', 'Notas'),            
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->fechaAlta == null) {
                $this->fechaAlta = \Yii::$app->formatter->asDatetime(new \DateTime);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * IdentityInterface
     */

    public static function findIdentity($id)
    {
        return Socio::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function siguienteId()
    {
        return Socio::find()
            ->select('(socio.id + 1)')
            ->orderBy('socio.id DESC')
            ->scalar();
    }

    public static function dataTable($desde, $limite, $termino, $orden = null)
    {
        $consulta = Socio::find();
        $total = $consulta->count();

        if ($termino) {            
            $consulta = $consulta->where(
                ['or',
                    ['like', 'CONCAT_WS(" ",nombre,apellidos)', $termino],
                    ['like', 'dni', $termino]
                ]
            );
        }

        $consulta = $consulta->offset($desde)->limit($limite);

        if ($orden && count($orden)) {
            $ordenables = ['id', 'nombre', 'dni'];
            if (isset($ordenables[$orden[0]['column']])) {
                $consulta = $consulta->orderBy($ordenables[$orden[0]['column']] . ' ' . $orden[0]['dir']);    
            }
        }

        $socios = $consulta->all();

        $resultado = [];

        $resultado['totales'] = [];
        $resultado['totales']['recordsTotal'] = $total;
        $resultado['totales']['recordsFiltered'] = $consulta->count();

        $resultado['socios'] = $socios;

        return $resultado;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return Socio::find()
            ->where(['email' => $username])
            ->orWhere(['nombreUsuario' => $username])
            ->one();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->nombreUsuario;
    }

    public function getNombreCompleto()
    {
        $nombreCompleto = $this->nombre . ' ' . $this->apellidos;
        
        return  $nombreCompleto;
    }

    public function getResumen()
    {
        return $this->nombreCompleto . ' - ' . $this->dni;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (!$this->password) {
            return false;
        }

        return \Yii::$app->passwordHash->validate_password($password, $this->password);
    }

    public function getLetraDniCorrecta()
    {
        if (!$this->dni) {
            return '';
        }

        $dni = intval($this->dni);
        return substr('TRWAGMYFPDXBNJZSQVHLCKE', $dni % 23, 1);
    }

    public function esCorrectaLetraDni()
    {
        if (!$this->dni) {
            return true;
        }

        $dni = intval($this->dni);
        $dni .= $this->letraDniCorrecta;

        return strcmp($this->dni, $dni) == 0;
    }
}
